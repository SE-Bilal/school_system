<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    
    // -------------------------------------------------------------------------
    public function __construct(){
	parent::__construct();
	$user_id = $this->session->userdata('u_id');
	if( !$user_id ){
	    redirect('/');
	}
    }
    // -------------------------------------------------------------------------    
    public function index()
    {
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/index');
        $this->load->view('include/footer');
    }
    // -------------------------------------------------------------------------    
    public function new_courses( $id = null )
    {
        $query  = $this->db->get('courses_added');
	$result['result'] = $query->result();
        $this->db->where('s_id',$id);
        $query  = $this->db->get('student');
	$result['result_1'] = $query->result();
        $result['id']     = $id;
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/student/add_courses',$result);
        $this->load->view('include/footer');
    }
    
// -------------------------------------------------------------------------
    
    public function create_more_courses_after_post( )
    {
        $course_name   = $this->input->post('courses'); 
        $category_name = $this->input->post('course_category');
        $subject_name  = $this->input->post('course_title');
        $course_duration   = $this->input->post('course_duration'); 
        $starting_date = $this->input->post('starting_date');
        $starting_date = explode("-",$starting_date);
        $starting_date[0] = substr( $starting_date[0], 2 );
        $starting_date = $starting_date[1] . '-' . $starting_date[2] . '-' . $starting_date[0];
        $ending_date  = $this->input->post('completion_date');
        $ending_date = explode("-",$ending_date);
        $ending_date[0] = substr( $ending_date[0], 2 );
        $ending_date = $ending_date[1] . '-' . $ending_date[2] . '-' . $ending_date[0];
        $admission_fee  = $this->input->post('admission_fee');
        $monthly_fee  = $this->input->post('monthly_fee');
        $tobepaid_or_paidfee = $this->input->post('tobepaid_or_paidfee');
        $entry_against = $this->input->post('entry_against');
        $description = $this->input->post('description');
        $ss_id = $this->input->post('ss_id');
        $created_date = mdate("%y-%m-%d"); 
        $this->db->where('s_id',$ss_id);
        $query  = $this->db->get('student');
	$result['result'] = $query->result();
        $fkstudent_id = $ss_id;
        $insert_courses_table = $this->db->insert('courses',
        [
            'fkstudent_id'      => $fkstudent_id,
            'course_name'       => $course_name,
            'course_category'   => $category_name,
            'category_subject'  => $subject_name,
            'course_duration'   => $course_duration,
            'starting_date'     => $starting_date,
            'ending_date'       => $ending_date,
            'created_at'        => $created_date
        ]);
         if(!empty($admission_fee)){            
            $reason_admission = 'Admission Fee';            
        }if(!empty ($monthly_fee)) {            
            $reason_monthly = 'Monthly Fee';            
        }        
        if(empty($tobepaid_or_paidfee)){            
            $tobepaid_or_paidfee_admission = 0;            
        }if (empty($tobepaid_or_paidfee)) {            
            $tobepaid_or_paidfee_monthly = 0;            
        }
        $insert_payment_table = $this->db->insert('payment',
            [
                'fkstudent_id'           => $fkstudent_id,
                'amount'                 => $admission_fee,
                'reason'                 => $reason_admission,
                'tobepaid_or_paid_fee'   => $tobepaid_or_paidfee_admission,
                'entry_against'          => $entry_against,
                'description'            => $description,
                'created_at'             => $created_date
            ]
        );
        $insert_payment_table = $this->db->insert('payment',
            [
                'fkstudent_id'           => $fkstudent_id,
                'amount'                 => $monthly_fee,
                'reason'                 => $reason_monthly,
                'tobepaid_or_paid_fee'   => $tobepaid_or_paidfee_monthly,
                'entry_against'          => $entry_against,
                'description'            => $description,
                'created_at'             => $created_date
            ]
        );       
        redirect(site_url().'admin/student_view');
    }
    // -------------------------------------------------------------------------
    
    public function student_coursedetails($id = null)
    {
        $this->db->select('*');
        $this->db->from('courses');
        $this->db->join('courses_added','courses_added.course_id = courses.course_name');
        $this->db->join('courses_category','courses_category.course_c_id = courses.course_category');
        $this->db->join('course_sub_category','course_sub_category.course_c_s_id = courses.category_subject');
        $this->db->where('courses.fkstudent_id',$id);        
        $query = $this->db->get();
        $result['result'] = $query->result();        
        $this->db->select('*');
        $this->db->from('student');
        $this->db->join('users','users.u_id = student.fkuser_id');
        $this->db->where('student.s_id',$id);        
        $query = $this->db->get();
        $result_1 = $query->result();
        $result['result_1'] = $result_1[0];
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/student/student_coursedetails',$result);
        $this->load->view('include/footer');
    }
    // -------------------------------------------------------------------------
    
    public function student_add()
    {
        $query  = $this->db->get('countries');
	$result['result'] = $query->result();        
        $this->db->select('*');
        $this->db->from('courses_added');
        $query = $this->db->get();
        $result['result_1'] = $query->result();
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/student/student_add',$result);
        $this->load->view('include/footer');
    }    
    // -------------------------------------------------------------------------

    public function student_view()
    {
        $this->db->select('*');
        $this->db->from('student');
        $this->db->join('users','users.u_id = student.fkuser_id');
        $query = $this->db->get();
        $result['result'] = $query->result();
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/student/student_view',$result);
        $this->load->view('include/footer');
    }    
    // -------------------------------------------------------------------------

    public function student_edit($s_id = null)
    {
        if($s_id == null)
        {
            redirect(site_url().'admin/student_view');
        }
        $this->db->select('*');
        $this->db->from('student',['student.s_id' => $s_id ]);
        $this->db->join('users','users.u_id = student.fkuser_id');
        $this->db->join('countries','countries.id = users.country_id');
        $this->db->join('states','states.id = users.province_id');
        $this->db->join('cities','cities.id = users.city_id');
        $this->db->join('courses','courses.fkstudent_id = student.s_id');
        $this->db->join('payment','payment.fkstudent_id = student.s_id');
        $this->db->join('courses_added','courses_added.course_id = courses.course_name');
        $this->db->where( 'student.s_id', $s_id );       
        $query = $this->db->get();
        $result = $query->result();
        $result['result'] = $result[0];
        $query  = $this->db->get('countries');
	$result['country'] = $query->result();
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/student/student_edit',$result);
        $this->load->view('include/footer');
    }
    //--------------------------------------------------------------------------
    
    public function student_details( $s_id = null ){
        $this->db->select('*');
        $this->db->from('student');
        $this->db->join('users','users.u_id = student.fkuser_id');
        $this->db->join('countries','countries.id = users.country_id');
        $this->db->join('states','states.id = users.province_id');
        $this->db->join('cities','cities.id = users.city_id');
        $this->db->join('courses','courses.fkstudent_id = student.s_id');
        $this->db->join('courses_added','courses_added.course_id = courses.course_name');
        $this->db->where( 'student.s_id', $s_id );
        $query = $this->db->get();
        $result= $query->result();
        $result['result'] = $result[0];
        $this->db->where( 'fkstudent_id', $result['result']->s_id);
        $query = $this->db->get('payment');
        $result['payment'] = $query->result();
        $query  = $this->db->get('countries');
	$result['country'] = $query->result();
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/student/student_details',$result);
        $this->load->view('include/footer');
    }
    // -------------------------------------------------------------------------

    public function create_student_after_post( $id = null )
    {
        $student_name = $this->input->post('name');
        $father_name = $this->input->post('f_name');
        $age = $this->input->post('age');
        $country = $this->input->post('country');
        $province = $this->input->post('province');
        $city = $this->input->post('city');
        $address = $this->input->post('address');
        $email = $this->input->post('email');
        $guardian_number = $this->input->post('g_number');       
        $course_name = $this->input->post('courses');
        $course_starting = $this->input->post('starting_date');
        $course_starting = explode("-",$course_starting);
        $course_starting[0] = substr( $course_starting[0], 2 );
        $course_starting = $course_starting[1] . '-' . $course_starting[2] . '-' . $course_starting[0];       
        $admission_fee = $this->input->post('admission_fee');
        $monthly_fee = $this->input->post('monthly_fee');
        $amount_received = $this->input->post('amount_received');
        $tobepaid_or_paidfee = $this->input->post('tobepaid_or_paidfee');
        $entry_against = $this->input->post('entry_against');
        $description = $this->input->post('description');            
        $created_date = mdate("%y-%m-%d"); 
        
        $this->db->select('*');
        $this->db->from('courses_category');
        $this->db->join('courses_added','courses_added.course_id = courses_category.fk_course_id');
        $this->db->where('courses_category.fk_course_id',$id);
        
        $query  = $this->db->get();
        $result['result'] = $query->result();
        
        $this->db->where('email', $email);
        $this->db->or_where('contact', $contact);
        $query = $this->db->get('users');
        $obj = $query->result();

        if (empty($obj)) {
            $insert_user_table = $this->db->insert('users',
                [
                    'name' => $student_name,
                    'f_name' => $father_name,
                    'age' => $age,
                    'email' => $email,
                    'country_id' => $country,
                    'province_id' => $province,
                    'city_id' => $city,
                    'address' => $address,
                    'created_at' => $created_date
                ]
            );
            $fkuser_id = $this->db->insert_id();
        } else {
            $fkuser_id = $obj[0]->u_id;
            $insert_user_table = $this->db->update('users',
                [
                    'name' => $student_name,
                    'f_name' => $father_name,
                    'age' => $age,
                    'email' => $email,
                    'country_id' => $country,
                    'province_id' => $province,
                    'city_id' => $city,
                    'address' => $address,
                    'created_at' => $created_date
                ],['s_id' => $fkuser_id]
            );
        }
        $insert_student_table = $this->db->insert('student',
            [
                'fkuser_id'         => $fkuser_id,
                'guardian_number'   => $guardian_number,
                'created_at'        => $created_date
            ]
        );
        $fkstudent_id = $this->db->insert_id();
        
        $insert_courses_table = $this->db->insert('courses',
            [
                'fkstudent_id'      => $fkstudent_id,
                'course_name'       => $course_name,
                'starting_date'     => $course_starting,
                'created_at'        => $created_date
            ]
        );        
        if(!empty($admission_fee)){            
            $reason_admission = 'Admission Fee';            
        }if(!empty ($monthly_fee)) {            
            $reason_monthly = 'Monthly Fee';            
        }if(!empty ($amount_received)) {            
            $reason_received = 'Received Fee';
        }        
        if(empty($tobepaid_or_paidfee)){            
            $tobepaid_or_paidfee_admission = 0;            
        }if (empty($tobepaid_or_paidfee)) {            
            $tobepaid_or_paidfee_monthly = 0;            
        }if (empty($tobepaid_or_paidfee)){            
            $tobepaid_or_paidfee_received = 1;
            }        
        $insert_payment_table = $this->db->insert('payment',
            [
                'fkstudent_id'           => $fkstudent_id,
                'amount'                 => $admission_fee,
                'reason'                 => $reason_admission,
                'tobepaid_or_paid_fee'   => $tobepaid_or_paidfee_admission,
                'entry_against'          => $entry_against,
                'description'            => $description,
                'created_at'             => $created_date
            ]
        );
        $insert_payment_table = $this->db->insert('payment',
            [
                'fkstudent_id'           => $fkstudent_id,
                'amount'                 => $monthly_fee,
                'reason'                 => $reason_monthly,
                'tobepaid_or_paid_fee'   => $tobepaid_or_paidfee_monthly,
                'entry_against'          => $entry_against,
                'description'            => $description,
                'created_at'             => $created_date
            ]
        );
        $insert_payment_table = $this->db->insert('payment',
            [
                'fkstudent_id'           => $fkstudent_id,
                'amount'                 => $amount_received,
                'reason'                 => $reason_received,
                'tobepaid_or_paid_fee'   => $tobepaid_or_paidfee_received,
                'entry_against'          => $entry_against,
                'description'            => $description,
                'created_at'             => $created_date
            ]
        );         
        redirect(site_url() . 'admin/student_view');
    }
    // -------------------------------------------------------------------------
    
    public function update_student_after_post($s_id = null,$u_id = null,$c_id = null, $p_id = null)
    {
        if ($s_id == null || $u_id == null) {
            redirect(site_url() . 'admin/student_view');
        } else {
        $student_name = $this->input->post('name');
        $father_name = $this->input->post('f_name');
        $age = $this->input->post('age');
        $country = $this->input->post('country');
        $province = $this->input->post('province');
        $city = $this->input->post('city');
        $address = $this->input->post('address');
        $email = $this->input->post('email');
        $guardian_number = $this->input->post('g_number');       
        $course_name = $this->input->post('courses');
        $course_starting = $this->input->post('starting_date');
        $updated_date = mdate("%y-%m-%d");
        echo $update_user_table = $this->db->update('users',
            [
                'name' => $student_name,
                'f_name' => $father_name,
                'age' => $age,
                'email' => $email,
                'country_id' => $country,
                'province_id' => $province,
                'city_id' => $city,
                'address' => $address,
                'updated_at' => $updated_date
            ], ['u_id' => $u_id]
        );

        $update_student_table = $this->db->update('student',
            [
                'guardian_number' => $guardian_number,
                'updated_at' => $updated_date
            ], ['s_id' => $s_id]
        );        
        $update_courses_table = $this->db->update('courses',
            [
                'course_name' => $course_name,
                'starting_date' => $course_starting,
                'updated_at' => $updated_date
            ], ['c_id' => $c_id]
        );      
        redirect(site_url() . 'admin/student_view');
        }
    } 
    // -------------------------------------------------------------------------

    public function student_delete($s_id = null , $u_id = null)
    {
        $this->db->delete('student',['s_id' => $s_id]);
        $this->db->delete('users',['u_id' => $u_id]);

        redirect(site_url() . 'admin/student_view');
    }

    // -------------------------------------------------------------------------
    
    public function teacher_add()
    {
       $query  = $this->db->get('countries');
	$result['result'] = $query->result();
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/teacher/teacher_add',$result);
        $this->load->view('include/footer');
    }
    // -------------------------------------------------------------------------
    public function teacher_view()
    {
        $this->db->select('*');
        $this->db->from('teacher');
        $this->db->join('users','users.u_id = teacher.fkuser_id');
        $query = $this->db->get();
        $result['result'] = $query->result();
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/teacher/teacher_view',$result);
        $this->load->view('include/footer');
    }   
    // -------------------------------------------------------------------------

    public function teacher_edit($t_id = null)
    {
        if($t_id == null)
        {
            redirect(site_url().'admin/teacher_view');
        }
        $this->db->select('*');
        $this->db->from('teacher');
        $this->db->join('users','users.u_id = teacher.fkuser_id');
        $this->db->join('countries','countries.id = users.country_id');
        $this->db->join('states','states.id = users.province_id');
        $this->db->join('cities','cities.id = users.city_id');
        $this->db->where( 'teacher.t_id', $t_id );
        $query = $this->db->get() ;
        $result = $query->result();
        $result['result'] = $result[0];
        $query  = $this->db->get('countries');
	$result['country'] = $query->result();
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/teacher/teacher_edit',$result);
        $this->load->view('include/footer');

    }
    //--------------------------------------------------------------------------
    
     public function teacher_details( $t_id = null )
      {        
        $this->db->select('*');
        $this->db->from('teacher');
        $this->db->join('users','users.u_id = teacher.fkuser_id');
        $this->db->join('countries','countries.id = users.country_id');
        $this->db->join('states','states.id = users.province_id');
        $this->db->join('cities','cities.id = users.city_id');
        $this->db->where( 'teacher.t_id', $t_id );
        $query = $this->db->get();
        $result = $query->result();
        $result['result'] = $result[0];
        $query  = $this->db->get('countries');
	$result['country'] = $query->result();
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/teacher/teacher_details',$result);
        $this->load->view('include/footer');
    }
    //--------------------------------------------------------------------------
      public function create_teacher_after_post()
      {
        $teacher_name = $this->input->post('name');
        $father_name = $this->input->post('f_name');
        $age = $this->input->post('age');
        $qualification = $this->input->post('qualification');
        $email = $this->input->post('email');
        $subject = $this->input->post('subject');
        $class_assign = $this->input->post('class_assign');
        $contact = $this->input->post('number');
        $salary = $this->input->post('salary');
        $country = $this->input->post('country');
        $province = $this->input->post('province');
        $city = $this->input->post('city');
        $address = $this->input->post('address');
        $description = $this->input->post('description');
        $created_date = mdate("%y-%m-%d");
        $this->db->where('email', $email);
        $this->db->or_where('contact', $contact);
        $query = $this->db->get('users');
        $obj = $query->result();
        if(empty($obj)) {
            $insert_user_table = $this->db->insert('users',
                [
                    'name' => $teacher_name,
                    'f_name' => $father_name,
                    'age' => $age,
                    'email' => $email,
                    'contact' => $contact,
                    'country_id' => $country,
                    'province_id' => $province,
                    'city_id' => $city,
                    'address' => $address,
                    'description' => $description,
                    'created_at' => $created_date
                ]
            );
            $fkuser_id = $this->db->insert_id();
        }
        else
        {
        $fkuser_id = $obj[0]->u_id;
        $insert_user_table = $this->db->update('users',
                [
                    'name' => $teacher_name,
                    'f_name' => $father_name,
                    'age' => $age,
                    'email' => $email,
                    'contact' => $contact,
                    'country_id' => $country,
                    'province_id' => $province,
                    'city_id' => $city,
                    'address' => $address,
                    'description' => $description,
                    'created_at' => $created_date
                ],['t_id' => $fkuser_id]
            );
        }
        $insert_teacher_table = $this->db->insert('teacher',
            [
                'fkuser_id' => $fkuser_id,
                'salary' => $salary,
                'qualification' => $qualification,
                'subject' => $subject,
                'class_assign' => $class_assign,
                'description' => $description,
                'created_at' => $created_date
            ]
        );
        $this->output->set_content_type('application_json');
        $this->output->set_output( json_encode([
            'result' => 1,
            'success'  => 'The Teacher have been successfully inserted'
        ]) );
        return FALSE;
     }
    //--------------------------------------------------------------------------
         public function update_teacher_after_post($t_id = null,$u_id = null){

        if ($t_id == null || $u_id == null) {
            redirect(site_url() . 'admin/teacher_view');
        } else {
            $teacher_name = $this->input->post('name');
            $father_name = $this->input->post('f_name');
            $age = $this->input->post('age');
            $qualification = $this->input->post('qualification');
            $reg_number = $this->input->post('reg_number');
            $email = $this->input->post('email');
            $subject = $this->input->post('subject');
            $class_assign = $this->input->post('class_assign');
            $contact = $this->input->post('number');
            $salary = $this->input->post('salary');
            $country = $this->input->post('country');
            $province = $this->input->post('province');
            $city = $this->input->post('city');
            $address = $this->input->post('address');
            $description = $this->input->post('description');
            $updated_date = mdate("%y-%m-%d");           
            $update_user_table = $this->db->update('users',
                [
                    'name' => $teacher_name,
                    'f_name' => $father_name,
                    'age' => $age,
                    'email' => $email,
                    'contact' => $contact,
                    'country_id' => $country,
                    'province_id' => $province,
                    'city_id' => $city,
                    'address' => $address,
                    'description' => $description,
                    'updated_at' => $updated_date
                ], ['u_id' => $u_id]
            );
            $update_teacher_table = $this->db->update('teacher',
                [
                   
                    'salary' => $salary,
                    'qualification' => $qualification,
                    'subject' => $subject,
                    'class_assign' => $class_assign,
                    'description' => $description,
                    'updated_at' => $updated_date
                ], ['t_id' => $t_id]
            );         
            $this->output->set_content_type('application_json');
            $this->output->set_output( json_encode([
                'result' => 1,
                'success'  => 'The Teacher has been successfully updated'
                   ]) );
            return FALSE;
               }
    }    
    //--------------------------------------------------------------------------
    
    public function teacher_delete($t_id = null , $u_id = null)
        {
            $this->db->delete('teacher',['t_id' => $t_id]);
            $this->db->delete('users',['u_id' => $u_id]);

            redirect(site_url() . 'admin/teacher_view');
        }

    // -------------------------------------------------------------------------

    public function visitor_view()
    {
        $this->db->select('*');
        $this->db->from('visitor');
        $this->db->join('users','users.u_id = visitor.fkuser_id');
        $query = $this->db->get();
        $result['result'] = $query->result();
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/visitor/visitor_view',$result);
        $this->load->view('include/footer');
    }
   
    // -------------------------------------------------------------------------
    
    public function visitor_edit($v_id = null)
    {
        if($v_id == null)
        {
            redirect(site_url().'admin/visitor_view');
        }
        $this->db->select('*');
        $this->db->from('visitor',['visitor.v_id' => $v_id ]);
        $this->db->join('users','users.u_id = visitor.fkuser_id');
        $this->db->join('courses_added','courses_added.course_id = visitor.courses');
        $this->db->join('countries','countries.id = users.country_id');
        $this->db->join('states','states.id = users.province_id');
        $this->db->join('cities','cities.id = users.city_id');
        $this->db->where( 'visitor.v_id', $v_id );
        $query = $this->db->get();
        $result = $query->result();
        $result['result'] = $result[0];
        $query  = $this->db->get('countries');
	$result['country'] = $query->result();
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/visitor/visitor_edit',$result);
        $this->load->view('include/footer');

    }    
     //--------------------------------------------------------------------------
    
    public function visitor_details( $v_id = null ){       
        $this->db->select('*');
        $this->db->from('visitor');
        $this->db->join('users','users.u_id = visitor.fkuser_id');
        $this->db->join('courses_added','courses_added.course_id = visitor.courses');
        $this->db->join('countries','countries.id = users.country_id');
        $this->db->join('states','states.id = users.province_id');
        $this->db->join('cities','cities.id = users.city_id');
        $this->db->where( 'visitor.v_id', $v_id );
        $query = $this->db->get();
        $result = $query->result();
        $result['result'] = $result[0];
        $query  = $this->db->get('countries');
	$result['country'] = $query->result();
        $this->load->view('include/header');
        $this->load->view('include/sidebar');
        $this->load->view('admin/visitor/visitor_details',$result);
        $this->load->view('include/footer');
        
    }    
    // -------------------------------------------------------------------------

    public function update_visitor_after_post($v_id = null,$u_id = null){

        if ($v_id == null || $u_id == null) {
            redirect(site_url() . 'admin/visitor_view');
        }             
        $visitor_name = $this->input->post('name');
        $father_name = $this->input->post('f_name');
        $age = $this->input->post('age');
        $profession = $this->input->post('profession');
        $email = $this->input->post('email');
        $contact = $this->input->post('number');
        $nic = $this->input->post('nic');
        $status = $this->input->post('status');
        $desire_courses = $this->input->post('desire_course');
        $country = $this->input->post('country');
        $province = $this->input->post('province');
        $city = $this->input->post('city');
        $address = $this->input->post('address');
        $description = $this->input->post('description');
        $updated_date = mdate("%y-%m-%d");
        echo $update_user_table = $this->db->update('users',
            [
                'name' => $visitor_name,
                'f_name' => $father_name,
                'age' => $age,
                'email' => $email,
                'contact' => $contact,
                'country_id' => $country,
                'province_id' => $province,
                'city_id' => $city,
                'address' => $address,
                'description' => $description,
                'updated_at' => $updated_date
            ], ['u_id' => $u_id]
        );
        $update_visitor_table = $this->db->update('visitor',
            [
                'profession'      => $profession,
                'nic'             => $nic,
                'status'          => $status,
                'courses'         => $desire_courses,
                'description'     => $description,
                'updated_at'      => $updated_date
            ], ['v_id'            => $v_id]
        );
        redirect(site_url() . 'admin/visitor_view');
    }
    // -------------------------------------------------------------------------

    public function visitor_delete($v_id = null , $u_id = null)
    {
        $this->db->delete('visitor',['v_id' => $v_id]);
        $this->db->delete('users',['u_id' => $u_id]);

        redirect(site_url() . 'admin/visitor_view');
    }
    // -------------------------------------------------------------------------

}
