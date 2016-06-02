<?php

class Tracks extends MX_Controller {

    var $partials = array();
    var $sitePermissions = array();
    var $num = 10;

    function __construct() {
        parent::__construct();

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }

        // load models and modules
        $this->load->helper('text');

        //$this->load->library('tags');
        $this->load->model('track_model', 'tracks');
        $this->load->module('pages');
    }

    function index() {
        //TRACK LISTINGS
        redirect('/tracks/track_list');
    }

    function track_details() {

        if (!$this->session->userdata('userID')) {
            redirect('/account/login');
        } else {

            // $data = $this->tracks->getTrackDetails($this->session->userdata('userID'));
            $data = $this->core->get_values($this->tracks->tracks_tbl, array('userID' => $this->session->userdata('userID')));


            $output['trackname'] = form_input('trackname', set_value('trackname', $data['trackname']), "class='form-control'");
            $output['address'] = form_input('address', set_value('address', $data['address']), "class='form-control'");
            $output['city'] = form_input('city', set_value('city', $data['city']), "class='form-control'");
            $output['state'] = display_states('state', set_value('t_state', $data['t_state']), "class='form-control'");
            $output['country'] = display_countries('country', set_value('t_country', $data['t_country']), "class='form-control'");
            $output['phone'] = form_input('phone', set_value('phone', $data['phone']), "class='form-control'");
            $output['email'] = form_input('email', set_value('email', $data['email']), "class='form-control'");
            $output['website'] = form_input('website', set_value('website', $data['website']), "class='form-control'");
            $output['facebook'] = form_input('facebook', set_value('facebook', $data['facebook']), "class='form-control'");
            $output['twitter'] = form_input('twitter', set_value('twitter', $data['twitter']), "class='form-control'");
            $output['twitter_widgetID'] = form_input('twitter_widgetID', set_value('twitter_widgetID', $data['twitter_widgetID']), "class='form-control'");
            $output['instagram'] = form_input('instagram', set_value('instagram', $data['instagram']), "class='form-control'");
            $output['youtube'] = form_input('youtube', set_value('youtube', $data['youtube']), "class='form-control'");
            $output['image'] = form_upload('image', '');
            $output['profile'] = $data['profile_img'];

            // $this->form_validation->set_rules(array(
            // array('field' => 'trackname', 'label' => 'Track Name','rules' => 'required|ucfirst'),
            // array('field' => 'address', 'label' => 'Address','rules' => 'required'),
            // array('field' => 'city', 'label' => 'City','rules' => 'required'),
            // array('field' => 'country', 'label' => 'Country','rules' => 'required'),
            // array('field' => 'phone', 'label' => 'Phone','rules' => 'required'),
            // array('field' => 'email', 'label' => 'Email','rules' => 'required'),
            // ));

            $this->core->required = array(
                'trackname' => array('label' => 'Track Name', 'rules' => 'required|ucfirst'),
                'address' => array('label' => 'Address', 'rules' => 'required'),
                'city' => array('label' => 'City', 'rules' => 'required'),
                'country' => array('label' => 'Country', 'rules' => 'required'),
                'phone' => array('label' => 'Phone', 'rules' => 'required'),
                'email' => array('label' => 'Email', 'rules' => 'required'),
            );

            //VERIFY DATA FIRST!
            if (count($_POST)) {

                //VERIFY IMAGE UPLOAD TOO!
                if ($oldFileName = @$_FILES['image']['name']) {
                    $this->uploads->allowedTypes = 'jpg|gif|png';
                    $this->uploads->maxWidth = '1000';
                    $this->uploads->maxHeight = '1000';
                    $this->uploads->maxSize = '5000';

                    if ($imageData = $this->uploads->upload_image(TRUE)) {
                        $this->core->set['profile_img'] = $imageData['file_name'];
                        $this->uploads->delete_file($data['profile_img']);
                    }

                    // get image errors if there are any
                    if ($this->uploads->errors) {
                        $this->form_validation->set_error($this->uploads->errors);
                    }
                }

                $this->core->set['lastUpdate'] = date('Y-m-d H:i:s');

                if ($this->core->update($this->tracks->tracks_tbl, array('userID' => $this->session->userdata('userID')))) {
                    $this->session->set_flashdata('message', 'Changes saved successfully!');
                    $this->tracks->sendUpdateEmail($data['tracksID'], 'details');
                    redirect(current_url());
                }
            }
        }

        $output['message'] = ($this->session->flashdata('message') ? $this->session->flashdata('message') : '');
        $output['errors'] = validation_errors();
        $this->pages->view('track_details', $output, TRUE);
    }

    function track_description() {
        if (!$this->session->userdata('userID')) {
            redirect('/account/login');
        } else {

            $trackdata = $this->tracks->getTrackDetails($this->session->userdata('userID'));

            // $data = $this->core->get_values($this->tracks->tracks_tbl,array('userID'=>$this->session->userdata('userID')));
            $qry = $this->db->get_where($this->tracks->tracks_tbl, array('userID' => $this->session->userdata('userID')));
            $data = $qry->row_array();



            $output['trackdesc'] = form_textarea('trackdesc', set_value('trackdesc', $data['trackdesc']), "class='form-control' rows='4' style='height:150px;' ");

            $this->db->order_by("trackcatOrder", "asc");
            $get_trackcats = $this->db->get($this->tracks->trackcat_tbl);
            $trackcats = $get_trackcats->result_array();

            foreach ($trackcats as $key => $val) {
                $get_trackXtrackcats = $this->db->get_where($this->tracks->trackXtrackcat_tbl, array('tracksID' => $trackdata['tracksID'], 'trackcatID' => $val['trackcatID']));
                $trackXtrackcats = $get_trackXtrackcats->row_array();

                $output['trackcats'][$key]['data1'] = '<div class="checkbox"><label>'
                        . form_checkbox('trackcat[' . $val['trackcatID'] . ']', 1, ($get_trackXtrackcats->num_rows() ? TRUE : FALSE))
                        . $val['track']
                        . '</label></div>';
            }

            $this->db->order_by("machinecatsOrder", "asc");
            $get_machinetype = $this->db->get($this->tracks->machinetype_tbl);
            $machinetype = $get_machinetype->result_array();

            foreach ($machinetype as $key => $val) {
                $get_trackXmachinecat = $this->db->get_where($this->tracks->trackXmachinecat_tbl, array('tracksID' => $trackdata['tracksID'], 'machinecatsID' => $val['machinecatsID']));
                $trackXmachinecat = $get_trackXmachinecat->row_array();

                $output['machinecats'][$key]['data2'] = '<div class="checkbox"><label>'
                        . form_checkbox('machinecat[' . $val['machinecatsID'] . ']', 1, ($get_trackXmachinecat->num_rows() ? TRUE : FALSE))
                        . $val['machine_type']
                        . '</label></div>';
            }

            // print_r($output['data1']);

            if (count($_POST)) {

                //delete then add
                $this->db->delete($this->tracks->trackXtrackcat_tbl, array('tracksID' => $trackdata['tracksID']));
                if (@$_POST['trackcat']) {
                    foreach ($_POST['trackcat'] as $key => $val) {
                        $this->db->insert($this->tracks->trackXtrackcat_tbl, array('tracksID' => $trackdata['tracksID'], 'trackcatID' => $key));
                    }
                }

                //delete then add
                $this->db->delete($this->tracks->trackXmachinecat_tbl, array('tracksID' => $trackdata['tracksID']));
                if (@$_POST['machinecat']) {
                    foreach ($_POST['machinecat'] as $key => $val) {
                        $this->db->insert($this->tracks->trackXmachinecat_tbl, array('tracksID' => $trackdata['tracksID'], 'machinecatsID' => $key));
                    }
                }

                $this->core->set['lastUpdate'] = date('Y-m-d H:i:s');

                if ($this->core->update($this->tracks->tracks_tbl, array('userID' => $this->session->userdata('userID')))) {
                    $this->session->set_flashdata('message', 'Changes saved successfully!');
                    $this->tracks->sendUpdateEmail($data['tracksID'], 'description');
                    redirect(current_url());
                }
            }
        }
        $output['message'] = ($this->session->flashdata('message') ? $this->session->flashdata('message') : '');
        $output['errors'] = validation_errors();
        $this->pages->view('track_description', $output, TRUE);
    }

    function track_photos() {
        if (!$this->session->userdata('userID')) {
            redirect('/account/login');
        } else {


            $output = '';

            $user = $this->tracks->getTrackDetails($this->session->userdata('userID'));
            $subscription = $this->tracks->getSubscriptionDetails($user['subscriptionID']);
            if ($this->tracks->get_no_of_images($user['tracksID'])) {
                $output['images'] = $this->tracks->get_images($user['tracksID']);
            } else {
                $output['images'] = FALSE;
            }

            if ($this->tracks->get_no_of_images($user['tracksID']) < $subscription['photos'] or $subscription['photos'] == -1) {
                //can add photo
                $output['upload'] = form_hidden('upload', 1) . form_upload('image', set_value('image'));
                $output['title'] = form_input('title', set_value('title'), "class='form-control'");
            } else {
                //cant add
                $output['upload'] = FALSE;
                $output['title'] = FALSE;
            }


            // $this->core->required = array(
            // 'title' => array('label' => 'Title', 'rules' => 'required|ucfirst'),
            // );

            if (count($_POST)) {
                //delete photos if something is checked

                if (@$_POST['delete_photos'] and @ $_POST['imagedata']) {
                    foreach ($_POST['imagedata'] as $key => $val) {
                        $img = $this->tracks->get_images($user['tracksID'], $key);
                        $this->db->delete($this->tracks->images_tbl, array('imgID' => $key));
                        $this->uploads->delete_file($img['image']);
                    }
                    $this->session->set_flashdata('message', 'Image(s) deleted successfully!');
                    redirect(current_url());
                }

                if (@$_POST['upload_photo']) {
                    //upload photos
                    //VERIFY IMAGE UPLOAD TOO!

                    if ($oldFileName = @$_FILES['image']['name']) {
                        $this->uploads->allowedTypes = 'jpg|gif|png';
                        $this->uploads->maxWidth = '5000';
                        $this->uploads->maxHeight = '5000';
                        $this->uploads->maxSize = '5000';

                        if ($imageData = $this->uploads->upload_image(TRUE)) {
                            $this->db->set('tracksID', $user['tracksID']);
                            $this->db->set('image', $imageData['file_name']);
                            $this->db->set('title', $this->input->post('title'));
                            //$this->uploads->delete_file($data['image']);
                        } else {
                            $this->session->set_flashdata('errors', 'No image selected!');
                            redirect(current_url());
                        }
                        // get image errors if there are any
                        if ($this->uploads->errors) {
                            $this->form_validation->set_error($this->uploads->errors);
                        } else {
                            //save to db
                            if ($this->db->insert($this->tracks->images_tbl)) {
                                $this->session->set_flashdata('message', 'Image added successfully!');
                                $this->tracks->sendUpdateEmail($user['tracksID'], 'photos');
                                redirect(current_url());
                            } else
                                $output['errors'] = "Image not saved !";
                        }
                    }else {
                        $this->session->set_flashdata('errors', 'No image selected!');
                        redirect(current_url());
                    }
                }
            }
        }

        $output['message'] = ($this->session->flashdata('message') ? $this->session->flashdata('message') : '');
        $output['errors'] = ($this->session->flashdata('errors') ? $this->session->flashdata('errors') : '');
        $this->pages->view('track_photos', $output, TRUE);
    }

    function track_videos() {

        if (!$this->session->userdata('userID')) {
            redirect('/account/login');
        } else {

            $output = '';

            $user = $this->tracks->getTrackDetails($this->session->userdata('userID'));
            $subscription = $this->tracks->getSubscriptionDetails($user['subscriptionID']);

            if ($this->tracks->get_no_of_videos($user['tracksID'])) {
                $output['videos'] = $this->tracks->get_videos($user['tracksID']);
            } else {
                $output['videos'] = FALSE;
            }

            if ($this->tracks->get_no_of_videos($user['tracksID']) < $subscription['videos'] or $subscription['videos'] == -1) {
                //can add photos
                $output['upload'] = form_input('video', set_value('video'), "class='form-control'");
                $output['title'] = form_input('title', set_value('title'), "class='form-control'");
            } else {
                //cant add
                $output['upload'] = FALSE;
                $output['title'] = FALSE;
            }


            if (count($_POST)) {
                //delete photos if something is checked
                if (@$_POST['delete_videos'] and @ $_POST['videodata']) {
                    foreach ($_POST['videodata'] as $key => $val) {
                        //$vid =  $this->tracks->get_videos($user['tracksID'], $key);
                        $this->db->delete($this->tracks->videos_tbl, array('vidID' => $key));
                        // $this->uploads->delete_file($vid['image']);
                    }
                    $this->session->set_flashdata('message', 'Video(s) deleted successfully!');
                    redirect(current_url());
                }

                if (@$_POST['upload_vids']) {
                    $required_fields[] = array('field' => 'video', 'label' => 'Youtube Link', 'rules' => 'required');
                    $required_fields[] = array('field' => 'title', 'label' => 'Title', 'rules' => 'required|ucfirst');

                    $this->form_validation->set_rules($required_fields);
                    $everything_validated = $this->form_validation->run();

                    if ($everything_validated) {
                        //save to db
                        $videolink = $this->tracks->get_youtubeID($this->input->post('video'));
                        $this->db->set('video', $videolink);
                        $this->db->set('title', $this->input->post('title'));
                        $this->db->set('tracksID', $user['tracksID']);
                        if ($this->db->insert($this->tracks->videos_tbl)) {
                            //success
                            $this->session->set_flashdata('message', 'Video added successfully!');
                            $this->tracks->sendUpdateEmail($user['tracksID'], 'videos');
                            redirect(current_url());
                        } else
                            $output['errors'] = "Video not saved!";
                    }
                }
            }
        }

        $output['message'] = ($this->session->flashdata('message') ? $this->session->flashdata('message') : '');
        $output['errors'] = validation_errors();
        //$output['errors'] = ($this->session->flashdata('errors')?$this->session->flashdata('errors'):'');
        $this->pages->view('track_videos', $output, TRUE);
    }

    function track_events() {
        if (!$this->session->userdata('userID')) {
            redirect('/account/login');
        } else {

            $output = '';
            $this->db->order_by('eventDate', 'desc');
            $data = $this->core->viewall($this->tracks->events_tbl, array('userID' => $this->session->userdata('userID')));
            foreach ($data['events'] as $key => $event) {
                $output['events1'][$key]['eventID'] = $event['eventID'];
                $output['events1'][$key]['eventTitle'] = $event['eventTitle'];
                $output['events1'][$key]['type'] = $event['type'];
                $output['events1'][$key]['description'] = character_limiter($event['description'], 15);
                $output['events1'][$key]['eventDate'] = date("d M Y", strtotime($event['eventDate']));
                $output['events1'][$key]['time'] = $event['time'];
            }


            $trackdata = $this->core->get_values($this->tracks->tracks_tbl, array('userID' => $this->session->userdata('userID')));
//		echo "<pre>";
//                print_r($trackdata);
//                echo "</pre>";
//                die();
//		$output['city'] = form_input('location',set_value('location'),"class='form-control' required");
//		$output['state'] = display_states('state',@$trackdata['state'],"class='form-control'");
            $output['title'] = form_input('eventTitle', set_value('eventTitle'), "class='form-control' required");

            $this->db->order_by('eventcatsOrder', 'asc');
            $get_eventcats = $this->db->get($this->tracks->eventcats_tbl);
            $eventcats[''] = 'Select Event';
            foreach ($get_eventcats->result_array() as $val) {
                $eventcats[$val['event_type']] = $val['event_type'];
            }
            $output['type'] = form_dropdown('type', $eventcats, '', "class='form-control' required");
            $output['start'] = form_input('eventDate', set_value('eventDate'), "class='datepicker form-control'");
            $output['end'] = form_input('eventEnd', set_value('eventEnd'), "class='datepicker form-control'");
            $eSched = array(
                'single' => "Single",
                'recur' => "Recurring",
            );
            $output['recur'] = form_dropdown('recur', $eSched, 'single', "id='recur' class='form-control'");
            // $output['recur'] = form_dropdown('recur',array('single'=>'Single','recur'=>'Recurring'),'single',"id='recur' class='form-control'");
            $option = array(
                '' => "==SELECT DAY==",
                'monday' => "Mon",
                'tuesday' => "Tue",
                'wednesday' => "Wed",
                'thursday' => "Thu",
                'friday' => "Fri",
                'saturday' => "Sat",
                'sunday' => "Sun",
            );
            $output['day'] = form_dropdown('day', $option, '', "class='form-control'");
            $output['time'] = form_input('time', set_value('time'), "class='form-control' required");
            $output['description'] = form_textarea('description', set_value('description'), "class='form-control' style='height:150px;' required");

            //VERIFY DATA FIRST!
            if (count($_POST)) {

                if (@$_POST['delete_events'] and @ $_POST['eventdata']) {
                    foreach ($_POST['eventdata'] as $key => $val) {
                        $this->db->delete($this->tracks->events_tbl, array('eventID' => $key));
                    }
                    $this->session->set_flashdata('message', 'Event(s) deleted successfully!');
                    redirect(current_url());
                }

                if (@$_POST['add_event']) {

                    $required_fields = array(
                                array(
                                    'field' => 'eventTitle',
                                    'label' => 'Title',
                                    'rules' => 'required'
                                ), array(
                                    'field' => 'type',
                                    'label' => 'Type',
                                    'rules' => 'required'
                                ), array(
                                    'field' => 'description',
                                    'label' => 'Description',
                                    'rules' => 'required'
                                ), array(
                                    'field' => 'time',
                                    'label' => 'Time',
                                    'rules' => 'required'
                                )
                    );

                    $success = FALSE;


                    if ($_POST['recur'] == 'recur') {
                        //
                        $required_fields[] = array('field' => 'day', 'label' => 'Day', 'rules' => 'required');

                        $this->form_validation->set_rules($required_fields);
                        $validation = $this->form_validation->run();

                        if ($validation == TRUE) {
                            $dow = $this->input->post('day');
                            $step = 1;
                            $unit = 'W';

                            $start = new DateTime($this->input->post('eventDate'));
                            $end = new DateTime($this->input->post('eventEnd'));

                            $start->modify($dow); // Move to first occurence
                            //$end->add(new DateInterval('P1Y')); // Move to 1 year from start

                            $interval = new DateInterval("P{$step}{$unit}");
                            $period = new DatePeriod($start, $interval, $end);
                            foreach ($period as $date) {
                                $this->core->set['eventTitle'] = $this->input->post('eventTitle');
                                $this->core->set['recur'] = $this->input->post('recur');
                                $this->core->set['description'] = $this->input->post('description');
                                $this->core->set['location'] = $trackdata['city'];
                                $this->core->set['state'] = $trackdata['t_state'];
                                $this->core->set['type'] = $this->input->post('type');
                                $this->core->set['time'] = $this->input->post('time');
                                $this->core->set['eventDate'] = $date->format('Y-m-d H:i:s');
                                $this->core->set['userID'] = $this->session->userdata('userID');

                                $success = $this->core->update($this->tracks->events_tbl);
                            }
                        }
                    } elseif ($_POST['recur'] == 'single') {

                        $this->form_validation->set_rules($required_fields);
                        $validation = $this->form_validation->run();
                        if ($validation == TRUE) {
                            $this->core->set['eventTitle'] = $this->input->post('eventTitle');
                            $this->core->set['recur'] = $this->input->post('recur');
                            $this->core->set['description'] = $this->input->post('description');
                            $this->core->set['location'] = $trackdata['city'];
                            $this->core->set['state'] = $trackdata['t_state'];
                            $this->core->set['type'] = $this->input->post('type');
                            $this->core->set['time'] = $this->input->post('time');
                            $this->core->set['eventDate'] = $this->input->post('eventDate');
                            $this->core->set['userID'] = $this->session->userdata('userID');

                            $success = $this->core->update($this->tracks->events_tbl);
                        }
                    }

                    if ($success == TRUE) {
                        $this->session->set_flashdata('message', 'Event saved succesfully!');
                        $trackdata = $this->tracks->getTrackDetails($this->session->userdata('userID'));
                        $this->tracks->sendUpdateEmail($trackdata['tracksID'], 'events');
                        redirect(current_url());
                    }
                }
            }
        }
        $output['message'] = ($this->session->flashdata('message') ? $this->session->flashdata('message') : '');
        //$output['errors'] = validation_errors();
        $this->pages->view('track_events', $output, TRUE);
    }

    function edit_track_event($id) {
        if (!$id)
            show_404();

        $data = $this->core->get_values($this->tracks->events_tbl, array('eventID' => $id, 'userID' => $this->session->userdata('userID')));
        $output['title'] = form_input('eventTitle', set_value('eventTitle', $data['eventTitle']), "class='form-control' required");

        $this->db->order_by('eventcatsOrder', 'asc');
        $get_eventcats = $this->db->get($this->tracks->eventcats_tbl);
        $eventcats[''] = 'Select Events';
        foreach ($get_eventcats->result_array() as $val) {
            $eventcats[$val['event_type']] = $val['event_type'];
        }
        $output['type'] = form_dropdown('type', $eventcats, set_value('type', $data['type']), "class='form-control' required");
        $eSched = array(
            'single' => "Single",
            'recur' => "Recurring",
        );
        $output['recur'] = form_dropdown('recur', $eSched, set_value('recur', $data['recur']), "class='form-control'");
        // $output['recur'] = form_dropdown('recur',set_value('recur',$data['recur']),"class='form-control'");
        $output['location'] = form_input('location', set_value('location', $data['location']), "class='form-control' required");
        $output['state'] = display_states('state', set_value('state', $data['state']), "class='form-control'");
        $output['start'] = form_input('eventDate', set_value('eventDate', date("Y-m-d", strtotime($data['eventDate']))), "class='datepicker form-control'");
        $output['end'] = form_input('eventEnd', set_value('eventEnd', date("Y-m-d", strtotime($data['eventEnd']))), "class='datepicker form-control'");
        $output['time'] = form_input('time', set_value('time', $data['time']), "class='form-control' required");
        $output['description'] = form_textarea('description', set_value('description', $data['description']), "class='form-control' style='height:50px;' required");

        $this->core->required = array(
            'type' => array('label' => 'Event Type', 'rules' => 'required'),
            'eventTitle' => array('label' => 'Event Title', 'rules' => 'required|ucfirst'),
            'description' => array('label' => 'Description', 'rules' => 'required|ucfirst'),
            'location' => array('label' => 'City', 'rules' => 'required|ucfirst'),
            'state' => array('label' => 'State', 'rules' => 'required'),
            'time' => array('label' => 'Time', 'rules' => 'required'),
            'eventDate' => array('label' => 'Start Date', 'rules' => 'required'),
            'eventEnd' => array('label' => 'End Date', 'rules' => 'required'),
        );
        if (count($_POST)) {
            $required_fields = array(
                        array(
                            'field' => 'eventTitle',
                            'label' => 'Title',
                            'rules' => 'required'
                        ), array(
                            'field' => 'type',
                            'label' => 'Type',
                            'rules' => 'required'
                        ), array(
                            'field' => 'description',
                            'label' => 'Description',
                            'rules' => 'required'
                        ), array(
                            'field' => 'location',
                            'label' => 'City',
                            'rules' => 'required'
                        ), array(
                            'field' => 'state',
                            'label' => 'State',
                            'rules' => 'required'
                        ), array(
                            'field' => 'time',
                            'label' => 'Time',
                            'rules' => 'required'
                        ), array(
                            'field' => 'eventDate',
                            'label' => 'Start',
                            'rules' => 'required'
                        ), array(
                            'field' => 'eventEnd',
                            'label' => 'Start',
                            'rules' => 'required'
                        )
            );
            $this->form_validation->set_rules($required_fields);
            $validation = $this->form_validation->run();

            if ($this->core->update($this->tracks->events_tbl, array('eventID' => $id, 'userID' => $this->session->userdata('userID')))) {
                $this->session->set_flashdata('message', 'Changes saved successfully!');
                redirect('tracks/track_events');
            }
        }
        $output['message'] = ($this->session->flashdata('message') ? $this->session->flashdata('message') : '');
        //$output['errors'] = validation_errors();
        $this->pages->view('edit_event', $output, TRUE);
    }

    function google_map() {
        if (!$this->session->userdata('userID')) {
            redirect('/account/login');
        } else {

            $output = '';

            $data = $this->core->get_values($this->tracks->tracks_tbl, array('userID' => $this->session->userdata('userID')));
            $output = $data;

            $output['lat'] = form_input('latitude', set_value('latitude', $data['latitude']), "class='form-control'");
            $output['long'] = form_input('longitude', set_value('longitude', $data['longitude']), "class='form-control'");
            $output['zoomlvl'] = form_input('zoom', set_value('zoom', $data['zoom']), "class='form-control'");

            $this->core->required = array(
                'latitude' => array('label' => 'Latitude', 'rules' => 'required'),
                'longitude' => array('label' => 'Longitude', 'rules' => 'required'),
                'zoom' => array('label' => 'Zoom Level', 'rules' => 'required'),
            );

            //VERIFY DATA FIRST!
            if (count($_POST)) {
                if ($this->core->update($this->tracks->tracks_tbl, array('userID' => $this->session->userdata('userID')))) {
                    $this->session->set_flashdata('message', 'Changes saved successfully!');
                    $this->tracks->sendUpdateEmail($data['tracksID'], 'google maps');
                    redirect(current_url());
                }
            }
        }
        $output['message'] = ($this->session->flashdata('message') ? $this->session->flashdata('message') : '');
        $output['errors'] = validation_errors();
        $this->pages->view('google_map', $output, TRUE);
    }

    function my_details() {
        if (!$this->session->userdata('userID')) {
            redirect('/account/login');
        } else {

            $data = $this->core->get_values($this->tracks->user_tbl, array('userID' => $this->session->userdata('userID')));

            // if($this->session->userdata('session_user') and $tracksid == ''){

            $output['firstName'] = form_input('firstName', set_value('firstName', $data['firstName']), "class='form-control'");
            $output['lastName'] = form_input('lastName', set_value('lastName', $data['lastName']), "class='form-control'");
            $output['email'] = form_input('email', set_value('email', $data['email']), "class='form-control'");
            $output['password'] = form_password('password', '', 'class="form-control"');
            $output['cpassword'] = form_password('cpassword', '', 'class="form-control"');
            $output['facebook'] = form_input('facebook', set_value('facebook', $data['facebook']), "class='form-control'");
            $output['twitter'] = form_input('twitter', set_value('twitter', $data['twitter']), "class='form-control'");

            $this->core->required = array(
                'firstName' => array('label' => 'First Name', 'rules' => 'required|ucfirst'),
                'lastName' => array('label' => 'Last Name', 'rules' => 'required|ucfirst'),
                'email' => array('label' => 'Email', 'rules' => 'required|valid_email'),
            );

            if (count($_POST)) {
                if (@$_POST['password'] or @ $_POST['cpassword']) {
                    $this->core->required = array(
                        'password' => array('label' => 'Password', 'rules' => 'trim|required|matches[cpassword]'),
                        'cpassword' => array('label' => 'Confirm Password', 'rules' => 'trim|required'),
                    );
                }

                if ($this->core->update($this->tracks->user_tbl, array('userID' => $this->session->userdata('userID')))) {
                    $this->session->set_flashdata('message', 'Changes saved successfully!');
                    redirect(current_url());
                }
            }
        }
        $output['message'] = ($this->session->flashdata('message') ? $this->session->flashdata('message') : '');
        $output['errors'] = validation_errors();
        $this->pages->view('my_details', $output, TRUE);
    }

    function my_subscription() {
        if (!$this->session->userdata('userID')) {
            redirect('/account/login');
        } else {

            $trackdata = $this->tracks->getTrackDetails($this->session->userdata('userID'));
            $data = $this->tracks->getSubscriptionDetails($trackdata['subscriptionID']);

            $output['subscriptionName'] = $data['subscriptionName'];
        }
        $this->pages->view('my_subscription', $output, TRUE);
    }

    function cancel_subscription() {

        $output = "";
        $output['message'] = "";
        if ($this->input->post('sendrequest')) {
            // send email to admin
            $emailHeader = str_replace('{name}', 'Administrator', $this->site->config['emailHeader']);
            $emailFooter = str_replace('{site:name}', $this->site->config['siteName'], $this->site->config['emailFooter']);
            $emailFooter = str_replace('{site:url}', $this->site->config['siteURL'], $emailFooter);

            $emailAccount = "A member has requested to cancel his/her subscription.";

            $this->load->library('email');
            $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
            $this->email->to($this->site->config['siteEmail']);
            $this->email->subject('"Subscription Cancellation" Request on ' . $this->site->config['siteName']);
            $this->email->message($emailHeader . "\n\n" . $emailAccount . "\n\n----------------------------------\nMember Name: " . trim($this->session->userdata('firstName') . ' ' . $this->session->userdata('lastName')) . "\nMember Email: " . $this->session->userdata('email') . "\n----------------------------------\n\n" . $emailFooter);
            $this->email->send();
            $output['message'] = "Your request has been sent.";
        }

        $this->pages->view('cancel_subscription', $output, TRUE);
    }

// TRACK PAGES
    function track_page($tracksid = '') {


        // if(!$this->session->userdata('session_user'))
        // {
        // if ($trackinfo = $this->get_trackinfo($tracksid))
        // {
        // $track_status = $trackinfo['status'];
        // if ($track_status == 0)
        // {
        // exit();
        // redirect('/tracks/track_list');
        // }
        // }
        // }
        // show track profile page
        // if($this->session->userdata('session_user') and $tracksid == ''){
        // $info=$this->tracks->getTrackDetails($this->session->userdata('userID'));
        // redirect('/tracks/track_page/'.$info['tracksID']);
        // }

        if ($this->session->userdata('session_user') and $tracksid == '') {


            $info = $this->tracks->getTrackDetails($this->session->userdata('userID'));
            redirect('/tracks/track_page/' . $info['tracksID']);
        }



        $data = $this->core->get_values($this->tracks->tracks_tbl, array('tracksID' => $tracksid));
        if (!$data)
            show_404();

        $output = $data;
        // print_r($data['country']);
        $output['country'] = @$data['t_country'];
        $output['state'] = @$data['t_state'];

        // $events = $this->core->viewall($this->tracks->events_tbl,array('userID'=>$data['userID'],'eventDate >='=>date('Y-m-d')),'eventDate');
        $get_events = $this->db->get_where($this->tracks->events_tbl, array('userID' => $data['userID'], 'eventDate >=' => date('Y-m-d')));
        $events = $get_events->result_array();
        // print_r($get_events->result_array());
        foreach ($events as $key => $event) {
            if ($event['type'] == 'RACE') {
                $output['events1'][$key]['indicator'] = 'success';
            } elseif ($event['type'] == 'PRACTICE') {
                $output['events1'][$key]['indicator'] = 'danger';
            } else {
                $output['events1'][$key]['indicator'] = '';
            }
            $output['events1'][$key]['eventID'] = $event['eventID'];
            $output['events1'][$key]['eventTitle'] = $event['eventTitle'];
            $output['events1'][$key]['type'] = $event['type'];
            $output['events1'][$key]['description'] = word_limiter($event['description'], 5);
            $output['events1'][$key]['eventDate'] = date("d M Y", strtotime($event['eventDate']));
            $output['events1'][$key]['eventEnd'] = date("d M Y", strtotime($event['eventEnd']));
            $output['events1'][$key]['time'] = $event['time'];
        }

        // $events1 = $this->core->viewall($this->tracks->events_tbl,array('userID'=>$data['userID'],'eventDate >='=>date('Y-m-d')),'eventDate',5);
        $get_events1 = $this->db->get_where($this->tracks->events_tbl, array('userID' => $data['userID'], 'eventDate >=' => date('Y-m-d')), 5);
        $events1 = $get_events1->result_array();
        foreach ($events1 as $key => $event) {
            if ($event['type'] == 'RACE') {
                $output['events2'][$key]['indicator'] = 'success';
            } elseif ($event['type'] == 'PRACTICE') {
                $output['events2'][$key]['indicator'] = 'danger';
            }
            $output['events2'][$key]['eventID'] = $event['eventID'];
            $output['events2'][$key]['eventTitle'] = $event['eventTitle'];
            $output['events2'][$key]['type'] = $event['type'];
            $output['events2'][$key]['description'] = word_limiter($event['description'], 5);
            $output['events2'][$key]['eventDate'] = date("d M Y", strtotime($event['eventDate']));
            $output['events2'][$key]['eventEnd'] = date("d M Y", strtotime($event['eventEnd']));
            $output['events2'][$key]['time'] = $event['time'];
        }


        $output['images_count'] = $this->tracks->get_no_of_images($tracksid);
        $output['images'] = $this->tracks->get_images($tracksid);
        $output['videos_count'] = $this->tracks->get_no_of_videos($tracksid);
        $output['videos'] = $this->tracks->get_videos($tracksid);

        $this->db->select('trackcat.track');
        $this->db->join($this->tracks->trackcat_tbl, "track_trackcat.trackcatID = trackcat.trackcatID");
        $this->db->order_by("trackcat.trackcatOrder", "asc");
        $get_trackXtrackcats = $this->db->get_where($this->tracks->trackXtrackcat_tbl, array('tracksID' => $tracksid));

        if (!$get_trackXtrackcats->num_rows()) {
            $output['trackcats'][0]['data1'] = '';
        }//FALSE;}
        else {
            $trackXtrackcats = $get_trackXtrackcats->result_array();
            foreach ($trackXtrackcats as $key => $val) {
                $output['trackcats'][$key]['data1'] = "<li>" . @$val['track'] . "</li>";
            }
        }


        $this->db->select('machinecats.machine_type');
        $this->db->join($this->tracks->machinetype_tbl, "track_machinecats.machinecatsID = machinecats.machinecatsID");
        $this->db->order_by('machinecatsOrder', 'asc');

        $get_trackXmachinecat = $this->db->get_where($this->tracks->trackXmachinecat_tbl, array('tracksID' => $tracksid));

        if (!$get_trackXmachinecat->num_rows()) {
            $output['machinecats'][0]['data2'] = '';
        }//FALSE;}
        else {
            $trackXmachinecat = $get_trackXmachinecat->result_array();
            foreach ($trackXmachinecat as $key => $val) {

                // $get_machinetype = $this->db->get_where($this->tracks->machinetype_tbl,array('machinecatsID'=>$val['machinecatsID']));
                // $machinetype = $get_machinetype->row_array();

                $output['machinecats'][$key]['data2'] = "<li>" . @$val['machine_type'] . "</li>";
            }
        }



        $this->core->required = array(
            'name' => array('label' => 'Name', 'rules' => 'required|ucfirst'),
            'email' => array('label' => 'Email', 'rules' => 'required|valid_email'),
        );
        if (count($_POST)) {
            $this->core->set['tracksID'] = $tracksid;
            $this->core->set['unsubscribeCode'] = md5($tracksid . date('Y-m-d H:i:s') . $this->input->post('name') . $this->input->post('email'));

            $check = $this->db->get_where($this->tracks->subscriber_tbl, array('tracksID' => $tracksid, 'email' => $this->input->post('email')));
            if ($check->num_rows() > 0)
                $this->form_validation->set_error('You are already subscribed to this track!');
            else {
                if ($this->core->update($this->tracks->subscriber_tbl)) {
                    //send an email to confirm subscription along with unsubscribe code
                    $this->tracks->sendSubscribeEmail($this->db->insert_id());
                    $this->session->set_flashdata('message', 'Thank you for subscribing to this track!');
                    redirect(current_url());
                }
            }
        }


        $output['message'] = ($this->session->flashdata('message') ? $this->session->flashdata('message') : '');
        $output['errors'] = validation_errors();
        $this->pages->view('track_profile', $output, TRUE);
    }

    function get_trackinfo($tracksid) {
        // default wheres
        // $this->db->where('tracksID', $tracksid);		
        // grab
        $query = $this->db->get_where('tracks', array('tracksID' => $tracksid), 1);
        // print_r($query);
        if ($query->num_rows()) {
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    function unsubscribeTrack($unsubscribeCode) {
        if (!$unsubscribeCode)
            show_404();
        $check = $this->db->get_where($this->tracks->subscriber_tbl, array('unsubscribeCode' => $unsubscribeCode));
        if ($check->num_rows() == 0)
            show_404();


        if ($this->db->delete($this->tracks->subscriber_tbl, array('unsubscribeCode' => $unsubscribeCode))) {
            echo "You have been unsubscribed to this tracks updates.";
        }
    }

    function track_list() {

        //search


        $output['country'] = display_countries('t_country', $this->input->get('t_country'), 'class="form-control"');
        $output['state'] = display_states('t_state', $this->input->get('t_state'), 'class="form-control "' . (@$_GET['t_country'] != 'US' or @ $_GET['t_country'] == '' ? "disabled" : ""));
        $output['keyword'] = form_input('keyword', $this->input->get('keyword'), 'class="form-control" placeholder="Enter Keyword"');
        $output['quick_search'] = form_input('key_word', $this->input->get('key_word'), 'class="form-control" placeholder="Search for Tracks"');

        // $output['eventtype'] =  form_dropdown('eventtype', array(), 'large');
        $this->db->order_by('machinecatsOrder', 'asc');
        $get_machine = $this->db->get($this->tracks->machinetype_tbl);
        $machine_type[''] = 'Please Select';
        foreach ($get_machine->result_array() as $val) {
            $machine_type[$val['machinecatsID']] = $val['machine_type'];
        }
        $output['machinetype'] = form_dropdown('machinecatsID', $machine_type, $this->input->get('machinecatsID'), 'class="form-control"');

        $this->db->order_by('trackcatOrder', 'asc');
        $get_trackcat = $this->db->get($this->tracks->trackcat_tbl);
        $trackcat[''] = 'Please Select';
        foreach ($get_trackcat->result_array() as $val) {
            $trackcat[$val['trackcatID']] = $val['track'];
        }
        $output['trackcat'] = form_dropdown('trackcatID', $trackcat, $this->input->get('trackcatID'), 'class="form-control"');

        $this->db->order_by('eventcatsOrder', 'asc');
        $get_eventcats = $this->db->get($this->tracks->eventcats_tbl);
        $eventcats[''] = 'Please Select';
        foreach ($get_eventcats->result_array() as $val) {
            $eventcats[$val['event_type']] = $val['event_type'];
        }
        $output['eventtype'] = form_dropdown('eventtype', $eventcats, $this->input->get('type'), 'class="form-control"');
        // $output['eventtype'] = form_dropdown('eventtype',array(''=>'Please Select','Practice'=>'PRACTICE','Race'=>'RACE'),$this->input->get('eventtype'),'class="form-control"');

        $this->db->order_by('subscriptionID', 'desc');
        $this->db->order_by('trackname', 'asc');

        //country state keyword machinetype trackcat eventtype
        if (isset($_GET['t_country']) OR isset($_GET['t_state']) OR isset($_GET['trackcatID']) OR isset($_GET['machinecatsID'])
                OR isset($_GET['eventtype']) OR isset($_GET['keyword']) OR isset($_GET['key_word'])) {
            $query = array();
            //country
            if (@$_GET['t_country'])
                $query['t_country'] = $this->input->get('t_country');
            //state
            if (@$_GET['t_state'])
                $query['ha_tracks.t_state'] = $this->input->get('t_state');
            //track cat
            if (@$_GET['trackcatID'])
                $query['trackcatID'] = $this->input->get('trackcatID');
            //machine cat
            if (@$_GET['machinecatsID'])
                $query['machinecatsID'] = $this->input->get('machinecatsID');
            //event type
            if (@$_GET['eventtype'])
                $query['ha_events.type'] = $this->input->get('eventtype');
            //keyword
            if (@$_GET['keyword'])
                $this->db->like('trackname', $this->input->get('keyword'));
            if (@$_GET['key_word'])
                $this->db->like('trackname', $this->input->get('key_word'));
            $this->db->where($query);


            $select = "ha_tracks.tracksID,ha_tracks.trackname,ha_tracks.profile_img,ha_tracks.address,ha_tracks.city,ha_tracks.t_state,ha_tracks.t_country,ha_tracks.phone,ha_tracks.email,ha_tracks.website,ha_tracks.facebook,ha_tracks.twitter,ha_tracks.instagram,ha_tracks.youtube,ha_tracks.subscriptionID,ha_events.type";
            // $select = "tracksID,trackname,profile_img,address,city,state,country,phone,email,website,facebook,twitter,instagram,youtube,subscriptionID";

            $this->db->select($select);
            $this->db->where('status', 1);
            $this->db->join($this->tracks->trackXmachinecat_tbl, 'ha_tracks.tracksID = ha_track_machinecats.tracksID', 'LEFT');
            $this->db->join($this->tracks->trackXtrackcat_tbl, 'ha_tracks.tracksID = ha_track_trackcat.tracksID', 'LEFT');
            $this->db->join($this->tracks->events_tbl, 'ha_tracks.userID = ha_events.userID', 'LEFT');

            //prevent searching old events when searching for events
            if (@$_GET['eventtype'])
                $this->db->where('ha_events.eventDate >=', date('Y-m-d'));

            $this->db->group_by('ha_tracks.tracksID');

            $this->db->order_by('subscriptionID', 'desc');
            $this->db->order_by('trackname', 'asc');

            if ($this->input->is_ajax_request()) {
                $offset = $this->input->get('offset');
                $track = $this->db->get($this->tracks->tracks_tbl, 20, $offset);
            } else {
                $this->db->where('status', 1);
                $track = $this->db->get($this->tracks->tracks_tbl, 20);



                // error_log($this->db->last_query());
                // echo $this->db->last_query();
            }

            if (!$track->num_rows()) {
                $track_rslt = 'Sorry, no tracks found';
            } else {
                $track_rslt = ' ';
            }

            $output['tracks'] = $track->result_array();
            $output['track_not_found'] = $track_rslt;
            $tracks['tracks'] = $track->result_array();
        } else {
            //$tracks = $this->core->viewall_tracks($this->tracks->tracks_tbl,'','',20);
            // echo'test';

            if ($this->input->is_ajax_request()) {
                $offset = $this->input->get('offset');
                $this->db->where('status', 1);
                $tracks_qry = $this->db->get($this->tracks->tracks_tbl, 20, $offset);
            } else {
                $this->db->where('status', 1);
                $tracks_qry = $this->db->get($this->tracks->tracks_tbl, 20);
            }

            $output['tracks'] = $tracks_qry->result_array();
            $tracks['tracks'] = $tracks_qry->result_array();
        }




        //parse tracks since halogy cant nest conditionals
        //process data here
        //remove duplicates



        $i = 1;
        $this->load->model('adverts/adverts_model', 'adverts');


        foreach ($tracks['tracks'] as $key => $val) {


            if (($i % 5) == 0 AND $i != 0) {
                $output['tracks'][$key]['ads_track'] = $this->adverts->viewAdvert('top');
            } else {
                $output['tracks'][$key]['ads_track'] = '';
            }
            $i++;






            //premium tracks
            if ($val['subscriptionID'] == 2) {


                $output['tracks'][$key]['data'] = "
						<div class='panel panel-primary'>
							<div class='panel-heading'>
								
								<h2 class='panel-title'>
								<div class='row'>
									<div class='col-xs-12 col-sm-9'><a href='" . site_url() . "tracks/track_page/" . $val['tracksID'] . "'>" . $val['trackname'] . "</div><div class='col-xs-12 col-sm-3'><button class='pull-right btn btn-success btn-xs'>View Full Profile</button></div></a></h2>
								</div>
							
							<div class='panel-body'>
							
								<div class='row'>
									
									<div class='col-xs-12 col-sm-3' style='height:250px;'>
										<a href='" . site_url() . "tracks/track_page/" . $val['tracksID'] . "'><img class='center-block img-thumbnail' src='" . ($val['profile_img'] ? "/static/uploads/" . $val['profile_img'] : 'holder.js/200x200/text:Profile Image') . "' style='max-height: 100%;max-width:100%;'/></a>
</div>
									<div class='col-xs-8 col-sm-7'>
										<ul class='list-unstyled track-list'>" .
                        "<li><img src='/static/new-elements/icon-address.gif' />" . @$val['address'] . " <i class='fa fa-fw fa-2x'></i>" . @$val['city'] . ", " . (@$val['t_state'] ? @$val['t_state'] . ', ' : '') . @$val['t_country'] . "</li>" .
                        (@$val['phone'] ? "<li><img src='/static/new-elements/icon-phone.gif' />" . $val['phone'] . "</li>" : '') .
                        (@$val['email'] ? "<li><a href='mailto:" . @$val['email'] . "'><img src='/static/new-elements/icon-email.gif' />" . @$val['email'] . "</a></li>" : '') .
                        ($val['website'] ? "<li><a target='_blank' href='" . @$val['website'] . "'><img src='/static/new-elements/icon-web.gif' />" . @$val['website'] . "</a></li>" : '') .
                        (@$val['facebook'] ? "<li><a target='_blank' href='" . @$val['facebook'] . "'><img src='/static/new-elements/icon-facebook.gif' />" . @$val['facebook'] . "</a></li>" : '') .
                        (@$val['twitter'] ? "<li><a target='_blank' href='http://www.twitter.com/" . @$val['twitter'] . "'><img src='/static/new-elements/icon-twitter.gif' />http://www.twitter.com/" . @$val['twitter'] . "</a></li>" : '') .
                        (@$val['instagram'] ? "<li><a target='_blank' href='" . @$val['instagram'] . "'><img src='/static/new-elements/icon-instagram.gif' />" . @$val['instagram'] . "</a></li>" : '') .
                        (@$val['youtube'] ? "<li><a target='_blank' href='" . @$val['youtube'] . "'><img src='/static/new-elements/icon-youtube.gif' />" . @$val['youtube'] . "</a></li>" : '') . "
										</ul>
									</div>
									<div class='col-xs-1 col-sm-1'><i class='fa fa-star fa-spin fa-4x center-block' style='color:gold'></i>Premium</div>
								</div>
							
							</div>
						</div>
						";
            } else {
                //regular tracks
                $output['tracks'][$key]['data'] = "
						<div class='panel panel-default'>
							<div class='panel-heading'>
								<h2 class='panel-title'><a href='" . site_url() . "tracks/track_page/" . $val['tracksID'] . "'>" . $val['trackname'] . "<button class='pull-right btn btn-success btn-xs'>View Full Profile</button></a></h2>
							</div>
							<div class='panel-body'>
								<div class='container'>
									<div class='col-xs-10'>
										<ul class='list-unstyled'>
											<li><img src='/static/new-elements/icon-address.gif' />" . $val['address'] . ", " . $val['city'] . "," . ($val['t_state'] ? $val['t_state'] . ', ' : '') . $val['t_country'] . "</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						";
            }
        }





        // $this->pagination->full_tag_open = '<ul class="pagination pull-right">';
        // $this->pagination->full_tag_close = '</ul>';
        // $this->pagination->first_tag_open = '<li>';
        // $this->pagination->last_tag_open = '<li>';
        // $this->pagination->cur_tag_open = '<li class="active"><a href=#>';
        // $this->pagination->next_tag_open = '<li>';
        // $this->pagination->prev_tag_open = '<li>';
        // $this->pagination->num_tag_open = '<li>';
        // $this->pagination->first_tag_close = '</li>';
        // $this->pagination->last_tag_close = '</li>';
        // $this->pagination->cur_tag_close = '</a></li>';
        // $this->pagination->next_tag_close = '</li>';
        // $this->pagination->prev_tag_close = '</li>';
        // $this->pagination->num_tag_close = '</li>';	
        // $this->pagination->full_tag_open = 'div class="pages">';
        // $this->pagination->full_tag_close = '</div>';
        // $output['pagination'] = $this->pagination->create_links();

        $output['message'] = ($this->session->flashdata('message') ? $this->session->flashdata('message') : '');
        $output['errors'] = validation_errors();

        if ($this->input->is_ajax_request()) {
            $i = 1;
            foreach ($output['tracks'] as $out) {

                if (($i % 5) == 0 AND $i != 0) {
                    $output['tracks'][$key]['ads_track'] = $this->adverts->viewAdvert('top');
                } else {
                    $output['tracks'][$key]['ads_track'] = '';
                }
                $i++;



                echo $out['data'];
                echo "<br /><br />";
                echo "<div class='track-advert' style='margin-bottom:50px;'>";
                echo $out['ads_track'];
                echo "</div>";
            }
        } else {
            $this->pages->view('track_listing', $output, TRUE);
        }
        //$this->pages->view('test-tracks',$output,TRUE);
    }

    function get_recent_tracks($offset = 0) {
        $result = $this->tracks->get_recent_tracks($offset);
        if (!empty($result)) {  
            $html="";
            $html.="<h4 class='mobileshow'>".  date("d F, Y",strtotime($result[0]['lastUpdate']))."</h4>";
            foreach ($result as $tracks) {
                $html .= "<div class='col-xs-12 col-sm-3 text-center update'>
                                <a href='" . site_url() . "tracks/track_page/" . $tracks['tracksID'] . "'>
                                <div style='height:150px;'>
                                        <img class='img-rounded img-thumbnail' src='" . site_url() . "static/uploads/" . $tracks['profile_img'] . "' style='max-width:90%;max-height:100%;' alt='" . $tracks['trackname'] . "'/>
                                </div>
                                        <p style='font-weight:bold;'>" . $tracks['trackname'] . "</p>
                                </a>
                        </div>";                
            }
            $html.="<div class=' mobilebanner recenttracks'>
                     <img src='/static/uploads/banner-middle.jpg' alt='banner1'/> </div>";
                echo $html;
        }
    }

    function search_by_map() {
        $get_tracks = $this->core->viewall_tracks($this->tracks->tracks_tbl);

        foreach ($get_tracks['tracks'] as $key => $val) {
            if ($val['latitude'] != '0.000000' or $val['longitude'] != '0.000000') {

                $output['tracks'][$key]['tracksID'] = $val['tracksID'];
                $output['tracks'][$key]['trackname'] = $val['trackname'];
                $output['tracks'][$key]['latitude'] = $val['latitude'];
                $output['tracks'][$key]['longitude'] = $val['longitude'];
                $output['tracks'][$key]['address'] = $val['address'];
                $output['tracks'][$key]['city'] = $val['city'];
                $output['tracks'][$key]['state'] = $val['t_state'];
                $output['tracks'][$key]['country'] = $val['t_country'];
                $output['tracks'][$key]['phone'] = $val['phone'];
            }
        }

        $output['message'] = ($this->session->flashdata('message') ? $this->session->flashdata('message') : '');
        $output['errors'] = validation_errors();
        $this->pages->view('track_listing_map', $output, TRUE);
    }

}
