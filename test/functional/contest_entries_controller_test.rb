require 'test_helper'

class ContestEntriesControllerTest < ActionController::TestCase
  fixtures :all
  def setup
    @user = Factory(:admin)
    sign_in_as @user
  end
  
  def test_should_get_index
    get :index
    assert_response :success
    assert_not_nil assigns(:contest_entries)
  end

  def test_should_get_new
    get :new
    assert_response :success
  end

  def test_should_create_contest_entry
    assert_difference('ContestEntry.count') do
      post :create, :contest_entry => { }
    end

    assert_redirected_to contest_entry_path(assigns(:contest_entry))
  end

  def test_should_show_contest_entry
    get :show, :id => contest_entries(:one).id
    assert_response :success
  end

  def test_should_get_edit
    get :edit, :id => contest_entries(:one).id
    assert_response :success
  end

  def test_should_update_contest_entry
    put :update, :id => contest_entries(:one).id, :contest_entry => { }
    assert_redirected_to contest_entry_path(assigns(:contest_entry))
  end

  def test_should_destroy_contest_entry
    assert_difference('ContestEntry.count', -1) do
      delete :destroy, :id => contest_entries(:one).id
    end

    assert_redirected_to contest_entries_path
  end
end
