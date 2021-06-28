<?php

namespace App\View\Components;

use App\Models\Todo;
use Illuminate\View\Component;

class TodoPending extends Component
{
    public $todo;

    public function __construct(Todo $todo)
    {
        $this->todo = $todo;
    }

    public function render()
    {
        return view('components.todo-pending');
    }
}
