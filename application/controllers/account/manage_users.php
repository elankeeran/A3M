<?php
/*
 * Manage_users Controller
 */
class Manage_users extends CI_Controller {

  /**
   * Constructor
   */
  function __construct()
  {
    parent::__construct();

    // Load the necessary stuff...
    $this->load->config('account/account');
    $this->load->helper(array('date', 'language', 'account/ssl', 'url'));
    $this->load->library(array('account/authentication', 'account/authorization', 'form_validation'));
    $this->load->model(array('account/account_model', 'account/account_details_model', 'account/acl_permission_model', 'account/acl_role_model', 'account/rel_account_permission_model', 'account/rel_account_role_model', 'account/rel_role_permission_model'));
    $this->load->language(array('general', 'account/manage_users', 'account/account_settings'));
  }

  /**
   * Manage Users
   */
  function index()
  {
    // Enable SSL?
    maintain_ssl($this->config->item("ssl_enabled"));

    // Redirect unauthenticated users to signin page
    if ( ! $this->authentication->is_signed_in())
    {
      redirect('account/sign_in/?continue='.urlencode(base_url().'account/manage_users'));
    }

    // Redirect unauthorized users to account profile page
    if ( ! $this->authorization->is_permitted('retrieve_users'))
    {
      redirect('account/account_profile');
    }

    // Retrieve sign in user
    $data['account'] = $this->account_model->get_by_id($this->session->userdata('account_id'));

    // Load manage users view
    $this->load->view('account/manage_users', $data);
  }

  function save($id=null)
  {
    // Enable SSL?
    maintain_ssl($this->config->item("ssl_enabled"));

    // Redirect unauthenticated users to signin page
    if ( ! $this->authentication->is_signed_in())
    {
      redirect('account/sign_in/?continue='.urlencode(base_url().'account/manage_users'));
    }

    // Redirect unauthorized users to manage users page
    if ( ! $this->authorization->is_permitted('update_users') && ! empty($id) )
    {
      redirect('account/manage_users');
    }

    // Redirect unauthorized users to manage users page
    if ( ! $this->authorization->is_permitted('create_users') && empty($id) )
    {
      redirect('account/manage_users');
    }

    // Retrieve sign in user
    $data['account'] = $this->account_model->get_by_id($this->session->userdata('account_id'));

    // Set action type
    $data['action'] = 'create';

    // Get the account to update
    if( ! empty($id) )
    {
      $data['update_account'] = $this->account_model->get_by_id($id);
      $data['update_account_details'] = $this->account_details_model->get_by_account_id($id);
      $data['action'] = 'update';
    }

    // Load manage users view
    $this->load->view('account/manage_users_save', $data);
  }

  function filter($type=null,$id=null)
  {
    $this->index();
  }
}

/* End of file manage_users.php */
/* Location: ./application/account/controllers/manage_users.php */