<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorksheetResultResource extends JsonResource
{
    protected $solutionId;

    // Kicsit átalakítjuk a konstruktort, hogy fogadja a solutionId-t
    public function __construct($resource, $solutionId = null)
    {
        parent::__construct($resource);
        $this->solutionId = $solutionId;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'student_name' => $this->student_name,
            // Itt a trükk: a kollekció minden elemének átadjuk a solutionId-t
            'tasks' => TaskResultResource::collection($this->whenLoaded('tasks'))
                ->additional(['solution_id' => $this->solutionId]),
        ];
    }
}
