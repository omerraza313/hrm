<?php

namespace App\Services;

use App\Enums\RolesEnum;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Address;
use App\Models\Employee;
use App\Models\Education;
use App\Models\Department;
use App\Models\Experience;
use App\Models\UserDetail;

class ProfileService {
    public function get_department_list(): object|array
    {
        return Department::all();
    }
    public function get_employee(string|int $id): array|object
    {
        $employee = User::where('id', $id)->withTrashed()->with([
            'emergency_contacts',
            'family_contacts',
            'address',
            'employee_details',
            'employee_details.manager' => function ($query) {
                $query->withTrashed();
            },
            'employee_details.designation' => function ($query) {
                $query->withTrashed();
            },
            'employee_details.department' => function ($query) {
                $query->withTrashed();
            },
            'bank',
            'educations' => function ($query) {
                $query->orderBy(
                    // Carbon::createFromFormat('d/m/Y', 'from_date')->format('Y-m-d')
                    DB::raw("STR_TO_DATE(start_date, '%d/%m/%Y')")
                    ,
                    'desc'
                )->get();
            },
            'experiences' => function ($query) {
                $query->orderBy(
                    // Carbon::createFromFormat('d/m/Y', 'from_date')->format('Y-m-d')
                    DB::raw("STR_TO_DATE(from_date, '%d/%m/%Y')")
                    ,
                    'desc'
                )->get();
            },
            'deactive_user'
        ])->first();
        return $employee;
    }

    public function get_designation(string|int $dep_id): array|object
    {
        $designations = Department::where('id', $dep_id)->with([
            'designations' => function ($query) {
                $query->get();
            }
        ])->first();
        return $designations;
    }
    public function store_update_education(array|object $education_list)
    {
        // dd($education_list);
        $this->delete_all_education($education_list['employee_id']);
        if (isset($education_list['edu_id']) || isset($education_list['edu_name']) || isset($education_list['edu_subject']) || isset($education_list['edu_start_date']) || isset($education_list['edu_complete_date']) || isset($education_list['edu_degree']) || isset($education_list['edu_grade'])) {
            for ($i = 0; $i < count($education_list['edu_name']); $i++) {
                Education::updateOrCreate(
                    ['id' => $education_list['edu_id'][$i]],
                    [
                        'name' => $education_list['edu_name'][$i],
                        'subject' => $education_list['edu_subject'][$i],
                        'start_date' => $education_list['edu_start_date'][$i],
                        'complete_date' => $education_list['edu_complete_date'][$i],
                        'degree' => $education_list['edu_degree'][$i],
                        'grade' => $education_list['edu_grade'][$i],
                        'user_id' => $education_list['employee_id'],
                    ]
                );
            }
        }
    }

    private function delete_all_education(string|int $emp_id)
    {
        $eduction = Education::where('user_id', $emp_id)->get();
        if ($eduction) {
            foreach ($eduction as $edu) {
                $edu->delete();
            }
        }
    }

    public function store_update_experience(array|object $experience_list)
    {
        // dd($experience_list['employee_id']);
        $this->delete_all_experience($experience_list['employee_id']);
        if (isset($experience_list['exp_id']) || isset($experience_list['exp_company_name']) || isset($experience_list['exp_location']) || isset($experience_list['exp_period_from']) || isset($experience_list['exp_period_to']) || isset($experience_list['exp_job_position'])) {
            for ($i = 0; $i < count($experience_list['exp_company_name']); $i++) {
                Experience::updateOrCreate(
                    ['id' => $experience_list['exp_id'][$i]],
                    [
                        'company_name' => $experience_list['exp_company_name'][$i],
                        'location' => $experience_list['exp_location'][$i],
                        'from_date' => $experience_list['exp_period_from'][$i],
                        'to_date' => $experience_list['exp_period_to'][$i],
                        'job_position' => $experience_list['exp_job_position'][$i],
                        'user_id' => $experience_list['employee_id'],
                    ]
                );
            }
        }
    }

    private function delete_all_experience(string|int $emp_id)
    {
        $experience = Experience::where('user_id', $emp_id)->get();
        if ($experience) {
            foreach ($experience as $edu) {
                $edu->delete();
            }
        }
    }

    public function store_update_profile_info(object|array $data)
    {
        $user = User::find($data['pro_user_id']);
        $user_detail = UserDetail::where('user_id', $user->id)->first();
        $address = Address::where('id', $data['pro_address_id'])->first();

        if (isset($data['pro_image']) && $data['pro_image']) {
            $image = time() . '.' . $data['pro_image']->extension();
            $data['pro_image']->move('images/employee/', $image);

            $user->update([
                'image' => $image
            ]);
        }


        $user->update([
            'first_name' => $data['pro_first_name'],
            'last_name' => $data['pro_last_name'],
        ]);

        $address->update([
            'address' => $data['pro_address'],
            'city' => $data['pro_city'],
            'state' => $data['pro_state'],
            'zip' => $data['pro_zip'],
            'country' => $data['pro_country'],
        ]);
        $new_dob = Carbon::createFromFormat('m/d/Y', $data['pro_dob'] )->format('Y-m-d');
        //dd([$new_dob, $data['pro_dob']]);
        //dd( $data);
        if ($user->hasRole(RolesEnum::Manager->value)) {
            $user_detail->update([
                'dob' => $new_dob,
                'phone' => $data['pro_number'],
                'gender' => $data['pro_gender'],
                'department_id' => $data['pro_department'],
            ]);
        } else {
            $user_detail->update([
                'dob' => $new_dob,
                'phone' => $data['pro_number'],
                'gender' => $data['pro_gender'],
                'department_id' => $data['pro_department'],
                'designation_id' => $data['pro_designation'],
            ]);
        }

    }
}
