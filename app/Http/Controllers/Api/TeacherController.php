<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TeacherController extends Controller
{
    /**
     * @return JsonResponse
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $data = Teacher::paginate(10);
        return success_response($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'name'          => 'required|min:4|unique:teachers,name',
            'gender'        => 'required|in:male,female',
            'religion'      => 'required|in:islam,hindu,christian',
            'phone'         => 'required|min:11|max:11|unique:teachers',
            'email'         => 'required|min:4|unique:teachers,email',
            'address'       => 'required',
            'date_of_birth' => 'required',
            'join_date'     => 'required',
            'photo'         => 'required',
            'username'      => 'required|unique:teachers,username',
            'password'      => 'required',
            'status'        => 'required'
        ]);
        if ($validation->fails()) return validation_error($validation->errors());
        try {
            $lastId         = Teacher::latest()->first()->id;
            $Teacher        = Teacher::create([
                'create_by'     => Auth::user()->id,
                'name'          => $request->name,
                'gender'        => $request->gender,
                'religion'      => $request->religion,
                'phone'         => $request->phone,
                'email'         => $request->email,
                'address'       => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'join_date'     => $request->join_date,
                'photo'         => "000000000",
                'username'      => $request->username,
                'password'      => bcrypt($request->password),
                'status'        => $request->status,
                'uu_id'         => str_pad($lastId, 6, 0, STR_PAD_LEFT)
            ]);
            $Teacher->photo = ImageUpload($request->photo, 'member')['path'];
            $Teacher->save();
            return success_response($Teacher, __('message.teacher.create.success'), Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return send_error(__('message.teacher.create.error'), $exception->getCode());
        }
    }

    /**
     * @param Teacher $teacher
     * @return JsonResponse
     * Display the specified resource.
     */
    public function show(Teacher $teacher): JsonResponse
    {
        return $teacher ? success_response($teacher) : send_error(__('message.teacher.manage.not_found'));
    }

    /**
     * @param Request $request
     * @param         $id
     * @return JsonResponse
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $teacher = Teacher::where('id', $id)->first();
        if ($teacher) {
            $validation = Validator::make($request->all(), [
                'name'          => 'required|min:4|unique:teachers,id,' . $id,
                'gender'        => 'required|in:male,female',
                'religion'      => 'required|in:islam,hindu,christian',
                'phone'         => 'required|min:11|max:11|unique:teachers,id,' . $id,
                'email'         => 'required|min:4|unique:teachers,id,' . $id,
                'address'       => 'required',
                'date_of_birth' => 'required',
                'join_date'     => 'required',
                'username'      => 'required|unique:teachers,id,' . $id,
                'password'      => 'required',
                'status'        => 'required'
            ]);
            if ($validation->fails()) return validation_error($validation->errors());
            try {
                $teacher->create_by     = Auth::user()->id;
                $teacher->name          = $request->name;
                $teacher->gender        = $request->gender;
                $teacher->religion      = $request->religion;
                $teacher->phone         = $request->phone;
                $teacher->email         = $request->email;
                $teacher->address       = $request->address;
                $teacher->date_of_birth = $request->date_of_birth;
                $teacher->join_date     = $request->join_date;
                $teacher->username      = $request->username;
                $teacher->password      = bcrypt($request->password);
                $teacher->status        = $request->status;
                if (strlen($request->photo) > 150) {
                    Storage::disk('public')->delete($teacher->photo);
                    $teacher->photo = ImageUpload($request->photo, 'member')['path'];
                }
                $teacher->save();
                return success_response($teacher, __('message.teacher.update.success'));
            } catch (Exception $exception) {
                return send_error(__('message.teacher.update.error'), $exception->getCode());
            }
        }
        return send_error(__('message.teacher.manage.not_found'));
    }

    /**
     * @param $id
     * @return JsonResponse
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $teacher = Teacher::where('uu_id', $id)->first();
        if ($teacher) {
            $teacher->delete();
            return success_response($teacher, __('message.teacher.manage.deleted'));
        }
        return send_error(__('message.teacher.manage.not_found'), Response::HTTP_NOT_FOUND);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changeStatus(Request $request): JsonResponse
    {
        try {
            $teacher = Teacher::where('id', $request->id)->first();
            $teacher->status === 'Active' ? $teacher->status = 'inactive' : $teacher->status = 'active';
            $teacher->save();
            return success_response($teacher, __('message.teacher.update.status'));
        } catch (Exception $exception) {
            return send_error(__('message.failed'), $exception->getCode());
        }
    }
}
