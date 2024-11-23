<?php

namespace App\Http\Controllers;

use App\Models\Task;
use DB;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::table('tasks')->insert([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            $data = Task::latest('id')->first();

            return jsonResponse("success", 'Data inserted successfully', 201, null, $data);
        } catch (\Throwable $th) {
            return jsonResponse("error", 'Something went wrong', 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Task::find($id);

        if (empty($data)) {
            return jsonResponse("error", 'No data found!!', 404);
        }

        try {
            $data->update([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
            ]);
            return jsonResponse("success", 'Data updated successfully', 201);
        } catch (\Throwable $th) {
            return jsonResponse("error", 'Something went wrong', 500);
        }
    }

    public function delete($id)
    {
        $data = Task::find($id);

        if (empty($data)) {
            return jsonResponse("error", 'No data found!!', 404);
        }

        try {
            $data->delete();
            return jsonResponse("success", 'Data deleted successfully', 201);
        } catch (\Throwable $th) {
            return jsonResponse("error", 'Something went wrong', 500);
        }
    }

    public function status($id, $status)
    {
        $data = Task::find($id);

        if (empty($data)) {
            return jsonResponse("error", 'No data found!!', 404);
        }

        try {

            if ($status == 'completed') {
                $data->update([
                    'status' => 'completed'
                ]);
            }

            if ($status == 'cancel') {
                $data->update([
                    'status' => 'cancel'
                ]);
            }

            return jsonResponse("success", 'Status updated', 201);
        } catch (\Throwable $th) {
            return jsonResponse("error", 'Something went wrong', 500);
        }
    }


}
