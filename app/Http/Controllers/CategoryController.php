<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $service,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CategoryResource::collection($this->service->getAll());
    }
}
