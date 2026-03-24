<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreWorksheetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',

            'subject_id' => 'required|integer|exists:subjects,id',

            'assignments' => 'required|array|min:1',
            'assignments.*.classroom_id' => [
                'required',
                'integer',
                // Ellenőrizzük, hogy létezik-e az osztály ÉS a tanáré-e
                \Illuminate\Validation\Rule::exists('classrooms', 'id')->where(function ($query) {
                    $query->where('user_id', Auth::id());
                }),
            ],
            'assignments.*.password' => 'required|string|min:4|max:8',

            'lifetime_minutes' => 'required|integer|min:1',
            'max_time_to_resolve_minutes' => 'required|integer|min:1',
            'max_points' => 'required|integer|min:1|max:100',
            'is_public' => 'required|boolean',

            'tasks' => 'required|array|min:1',

            'tasks.*.task_title' => 'required|string|max:255',
            'tasks.*.task_description' => 'nullable|string',
            'tasks.*.task_type_id' => 'required|integer|exists:task_types,id',

        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A feladatlap cím megadása kötelező.',
            'assignments.*.classroom_id.exists' => 'Az egyik kiválasztott osztály nem létezik, vagy nincs jogosultságod hozzá.',
            'tasks.required' => 'Legalább egy feladatot hozzá kell adni.',

            'tasks.*.task_title.required' => 'Minden feladatnak kell címet adni.',

            'tasks.*.assignment.imgURL.required_if' => 'Az assignment típusú feladatnál kötelező a kép.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            foreach ($this->tasks ?? [] as $index => $task) {
                $this->validateTaskByType($validator, $task, $index);
            }

        });
    }

    private function validatePairing($validator, $task, $index)
    {
        $groups = $task['pairing']['pairing_groups'] ?? [];

        if (count($groups) > 8) {
            $validator->errors()->add("tasks.$index.pairing.pairing_groups", 'Maximum 8 párt alkothatsz.');

            return;
        }

        if (empty($groups)) {
            $validator->errors()->add("tasks.$index.pairing.pairing_groups", 'Legalább egy pár megadása kötelező.');

            return;
        }

        foreach ($groups as $gIndex => $group) {
            $pairQuestion = $group['pair_question'] ?? null;
            $pairQuestionImage = $group['pair_question_image'] ?? null;

            $hasPairQuestion = ! empty($pairQuestion);
            // String alapú ellenőrzés az async elérési úthoz
            $hasPairQuestionImage = ! empty($pairQuestionImage) && is_string($pairQuestionImage);

            $pairAnswer = $group['pair_answer'] ?? null;
            $pairAnswerImage = $group['pair_answer_image'] ?? null;

            $hasPairAnswer = ! empty($pairAnswer);
            $hasPairAnswerImage = ! empty($pairAnswerImage) && is_string($pairAnswerImage);

            // 1. Logikai validáció: Kérdés oldal
            if (! $hasPairQuestion && ! $hasPairQuestionImage) {
                $validator->errors()->add("tasks.$index.pairing.pairing_groups.$gIndex", 'A párosításhoz meg kell adni vagy egy kérdés szöveget, vagy egy képet.');
            }

            if ($hasPairQuestion && $hasPairQuestionImage) {
                $validator->errors()->add("tasks.$index.pairing.pairing_groups.$gIndex", 'Nem adhatsz meg egyszerre kérdés szöveget és képet.');
            }

            // 2. Logikai validáció: Válasz oldal
            if (! $hasPairAnswer && ! $hasPairAnswerImage) {
                $validator->errors()->add("tasks.$index.pairing.pairing_groups.$gIndex", 'A párosításhoz meg kell adni vagy egy válasz szöveget, vagy egy képet.');
            }

            if ($hasPairAnswer && $hasPairAnswerImage) {
                $validator->errors()->add("tasks.$index.pairing.pairing_groups.$gIndex", 'Nem adhatsz meg egyszerre válasz szöveget és képet.');
            }

            // 3. Összefüggés: Ha van kérdés, kell válasz is
            if (($hasPairQuestion || $hasPairQuestionImage) && (! $hasPairAnswerImage && ! $hasPairAnswer)) {
                $validator->errors()->add("tasks.$index.pairing.pairing_groups.$gIndex", 'Ha van kérdés, kötelező választ is megadni.');
            }

            // 4. Szöveges mezők validálása
            if ($hasPairQuestion) {
                if (strlen($pairQuestion) > 130) {
                    $validator->errors()->add("tasks.$index.pairing.pairing_groups.$gIndex.pair_question", 'A kérdés nem lehet hosszabb, mint 130 karakter.');
                }
            }

            if ($hasPairAnswer) {
                if (strlen($pairAnswer) > 130) {
                    $validator->errors()->add("tasks.$index.pairing.pairing_groups.$gIndex.pair_answer", 'A válasz nem lehet hosszabb, mint 130 karakter.');
                }
            }

            // 5. Kép elérési utak validálása a segédfüggvénnyel
            if ($hasPairQuestionImage) {
                $this->validateImagePath($validator, $pairQuestionImage, "tasks.$index.pairing.pairing_groups.$gIndex.pair_question_image");
            }

            if ($hasPairAnswerImage) {
                $this->validateImagePath($validator, $pairAnswerImage, "tasks.$index.pairing.pairing_groups.$gIndex.pair_answer_image");
            }
        }
    }

    private function validateGrouping($validator, $task, $index)
    {
        $groups = $task['grouping']['groups'] ?? [];

        // Csoportok számának ellenőrzése (javítottam a hibaüzenetet is a logikádhoz)
        if (count($groups) > 4) {
            $validator->errors()->add(
                "tasks.$index.grouping.groups",
                'Maximum 4 csoportot alkothatsz.'
            );

            return;
        }

        if (empty($groups)) {
            $validator->errors()->add(
                "tasks.$index.grouping.groups",
                'Legalább egy csoport megadása kötelező.'
            );

            return;
        }

        foreach ($groups as $gIndex => $group) {
            $groupName = $group['name'] ?? null;

            // Csoport név ellenőrzése
            if (empty($groupName)) {
                $validator->errors()->add("tasks.$index.grouping.groups.$gIndex.name", 'A név kötelező szöveg');
            } elseif (strlen($groupName) > 30) {
                $validator->errors()->add("tasks.$index.grouping.groups.$gIndex.name", 'A megadott csoportnév túl hosszú.');
            }

            $items = $group['items'] ?? [];

            // Csoport elemek számának ellenőrzése
            if (count($items) > 5) {
                $validator->errors()->add("tasks.$index.grouping.groups.$gIndex.items", 'Maximum 5 elem lehet egy csoportban.');
            }
            if (empty($items)) {
                $validator->errors()->add("tasks.$index.grouping.groups.$gIndex.items", 'Üres csoportot nem hozhatsz létre.');
            }

            foreach ($items as $gItemIndex => $groupItem) {
                $name = $groupItem['name'] ?? null;
                $image = $groupItem['image'] ?? null;

                $hasName = ! empty($name);
                // String alapú ellenőrzés (temp/ vagy meglévő path)
                $hasImage = ! empty($image) && is_string($image);

                // 1. Logikai ellenőrzés: Név VAGY kép
                if (! $hasName && ! $hasImage) {
                    $validator->errors()->add(
                        "tasks.$index.grouping.groups.$gIndex.items.$gItemIndex",
                        'A csoport elemhez vagy nevet vagy képet kell megadni.'
                    );

                    continue;
                }

                if ($hasName && $hasImage) {
                    $validator->errors()->add(
                        "tasks.$index.grouping.groups.$gIndex.items.$gItemIndex",
                        'Egy csoport elemhez nem adhatsz meg egyszerre nevet és képet.'
                    );

                    continue;
                }

                // 2. Név hossza
                if ($hasName && strlen($name) > 30) {
                    $validator->errors()->add(
                        "tasks.$index.grouping.groups.$gIndex.items.$gItemIndex.name",
                        'A csoport elem nem lehet hosszabb 30 karakternél.'
                    );
                }

                // 3. Kép elérési út validálása
                if ($hasImage) {
                    $this->validateImagePath(
                        $validator,
                        $image,
                        "tasks.$index.grouping.groups.$gIndex.items.$gItemIndex.image"
                    );
                }
            }
        }
    }

    private function validateShortAnswer($validator, $task, $index)
    {
        $questions = $task['short_answer']['questions'] ?? [];

        if (count($questions) > 18) {
            $validator->errors()->add("tasks.$index.short_answer.questions", 'Maximum 18 kérdést alkothatsz.');

            return;
        }

        if (empty($questions)) {
            $validator->errors()->add("tasks.$index.short_answer.questions", 'Legalább egy kérdés megadása kötelező.');

            return;
        }

        foreach ($questions as $gIndex => $shortAnswer) {
            $shortAnswerQuestion = $shortAnswer['question'] ?? null;
            $shortAnswerQuestionImage = $shortAnswer['question_image'] ?? null;

            $hasQuestion = ! empty($shortAnswerQuestion);

            // VÁLTOZÁS: Itt már csak azt nézzük, hogy string-e és van-e benne tartalom
            $hasQuestionImage = ! empty($shortAnswerQuestionImage) && is_string($shortAnswerQuestionImage);

            $shortAnswerAnswer = $shortAnswer['answer'] ?? null;
            $shortAnswerImage = $shortAnswer['answer_image'] ?? null;

            $hasAnswer = ! empty($shortAnswerAnswer);
            $hasAnswerImage = ! empty($shortAnswerImage) && is_string($shortAnswerImage);

            // 1. Logikai ellenőrzések (Szöveg VAGY kép)
            if (! $hasQuestion && ! $hasQuestionImage) {
                $validator->errors()->add("tasks.$index.short_answer.questions.$gIndex", 'A kérdéshez meg kell adni szöveget vagy képet.');
            }

            if ($hasQuestion && $hasQuestionImage) {
                $validator->errors()->add("tasks.$index.short_answer.questions.$gIndex", 'Nem adhatsz meg egyszerre kérdés szöveget és képet.');
            }

            if (! $hasAnswer && ! $hasAnswerImage) {
                $validator->errors()->add("tasks.$index.short_answer.questions.$gIndex", 'A válaszhoz meg kell adni szöveget vagy képet.');
            }

            if ($hasAnswer && $hasAnswerImage) {
                $validator->errors()->add("tasks.$index.short_answer.questions.$gIndex", 'Nem adhatsz meg egyszerre válasz szöveget és képet.');
            }

            // 2. Szöveges mezők hosszának ellenőrzése
            if ($hasQuestion && strlen($shortAnswerQuestion) > 150) {
                $validator->errors()->add("tasks.$index.short_answer.questions.$gIndex.question", 'A kérdés max 150 karakter.');
            }

            if ($hasAnswer && strlen($shortAnswerAnswer) > 50) {
                $validator->errors()->add("tasks.$index.short_answer.questions.$gIndex.answer", 'A válasz max 50 karakter.');
            }

            // 3. Kép elérési út validálása
            // VÁLTOZÁS: Itt a validateIMG helyett egy egyszerű string/path validáció kell
            if ($hasQuestionImage) {
                $this->validateImagePath($validator, $shortAnswerQuestionImage, "tasks.$index.short_answer.questions.$gIndex.question_image");
            }

            if ($hasAnswerImage) {
                $this->validateImagePath($validator, $shortAnswerImage, "tasks.$index.short_answer.questions.$gIndex.answer_image");
            }
        }
    }

    private function validateAssignment($validator, $task, $index)
    {
        $assignment = $task['assignment'] ?? null;
        $assignmentImage = $assignment['image'] ?? null;

        // 1. Kép ellenőrzése (Async path string)
        if (! $assignmentImage) {
            $validator->errors()->add(
                "tasks.$index.assignment.image",
                'Kötelező képet megadni.'
            );
        } else {
            // A korábban létrehozott string-alapú validátor hívása
            $this->validateImagePath($validator, $assignmentImage, "tasks.$index.assignment.image");
        }

        // 2. Koordináták globális ellenőrzése
        $coordinates = $assignment['coordinatesAndAnswers'] ?? [];

        if (empty($coordinates)) {
            $validator->errors()->add(
                "tasks.$index.assignment.coordinatesAndAnswers",
                'Legalább 1 koordináta megadása kötelező.'
            );

            return; // Ha nincs koordináta, felesleges tovább menni a ciklusba
        }

        if (count($coordinates) > 10) {
            $validator->errors()->add(
                "tasks.$index.assignment.coordinatesAndAnswers",
                'Maximum 10 koordinátát adhatsz meg.'
            );
        }

        // 3. Koordináták és válaszok részletes ellenőrzése
        foreach ($coordinates as $cIndex => $coordinate) {

            // Koordináta adatok megléte (pl. "x,y,w,h" string)
            if (empty($coordinate['coordinate'])) {
                $validator->errors()->add(
                    "tasks.$index.assignment.coordinatesAndAnswers.$cIndex.coordinate",
                    'A koordináta megadása kötelező.'
                );
            }

            $answers = $coordinate['answers'] ?? [];

            // Válaszok száma koordinátánként
            if (count($answers) > 2) {
                $validator->errors()->add(
                    "tasks.$index.assignment.coordinatesAndAnswers.$cIndex.answers",
                    'Egy koordinátához maximum 2 válasz adható meg.'
                );
            }

            // Helyes válaszok számlálása
            $correctCount = collect($answers)->where('isCorrect', true)->count();

            if ($correctCount < 1) {
                $validator->errors()->add(
                    "tasks.$index.assignment.coordinatesAndAnswers.$cIndex.answers",
                    'Legalább 1 helyes választ meg kell adni.'
                );
            }

            if ($correctCount > 1) {
                $validator->errors()->add(
                    "tasks.$index.assignment.coordinatesAndAnswers.$cIndex.answers",
                    'Maximum 1 helyes válasz adható meg.'
                );
            }

            // Konkrét válaszszövegek ellenőrzése
            foreach ($answers as $aIndex => $answer) {
                if (empty($answer['answer'])) {
                    $validator->errors()->add(
                        "tasks.$index.assignment.coordinatesAndAnswers.$cIndex.answers.$aIndex.answer",
                        'A válasz megadása kötelező.'
                    );
                }
            }
        }
    }

    private function validateTaskByType($validator, $task, $index)
    {
        return match ($task['task_type_id']) {
            1 => $this->validateGrouping($validator, $task, $index),
            2 => $this->validatePairing($validator, $task, $index),
            3 => $this->validateShortAnswer($validator, $task, $index),
            4 => $this->validateAssignment($validator, $task, $index),
            default => null,
        };
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validációs hiba történt.',
            'errors' => $validator->errors(),
        ], 422));
    }

    private function validateImagePath($validator, $path, $errorKey)
    {
        if (! is_string($path)) {
            $validator->errors()->add($errorKey, 'A kép formátuma érvénytelen.');

            return;
        }

        $isTemp = str_starts_with($path, 'temp/');
        $isFinal = str_contains($path, 'group-items/original/');

        if (! $isTemp && ! $isFinal) {
            $validator->errors()->add($errorKey, 'Érvénytelen kép útvonal.');

            return;
        }

        if (! Storage::disk('public')->exists($path)) {
            $validator->errors()->add(
                $errorKey,
                'A megadott kép már nem található (valószínűleg már feldolgozásra került vagy lejárt).'
            );
        }
    }
}
