<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

class TasksController extends Controller
{
    public function index()
    {
        // $tasks = task::all();
        
        // return view('tasks.index',[
        //     'tasks' => $tasks,
        // ]);
        
        $data = [];
        if (\Auth::check()) {
            $user = \Auth::user();
            $tasks = $user->tasks()->orderBy('created_at', 'asc')->paginate(10);

            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
            return view('tasks.index', $data);
        }else {
            return view('welcome');
        }
    }

    public function create()
    {
        $task = new Task;
        
        return view('tasks.create',[
            'task' => $task,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:191',
            'status' => 'required|max:10'
        ]);
        
        // $task = new Task;
        // $task->content = $request->content;
        // $task->status = $request->status;
        // $task->save();

        $request->user()->tasks()->create([
            'content' => $request->content,
            'status' => $request->status,
        ]);

        return redirect('tasks');
    }

    public function show($id)
    {
        $task = \App\Task::find($id);

        if(\Auth::id() === $task->user_id) {
            return view('tasks.show',[
                'task' => $task,
            ]);
        }else{
            return redirect('tasks');
        }
    }

    public function edit($id)
    {
        $task = Task::find($id);
        
        return view('tasks.edit',[
            'task' => $task,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'content' => 'required|max:191',
            'status' => 'required|max:10'
        ]);
        
        $task = \App\Task::find($id);
        
        if (\Auth::id() === $task->user_id) {
            $task->content = $request->content;
            $task->status = $request->status;
            $task->save();
        }
        
        return redirect('tasks');
    }

    public function destroy($id)
    {
        // $task = Task::find($id);
        // $task->delete();
        
        $task = \App\Task::find($id);

        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }

        return redirect('tasks');
    }
}
