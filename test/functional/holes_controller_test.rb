require 'test_helper'

class HolesControllerTest < ActionController::TestCase
  fixtures :all
  def setup
    @user = Factory(:admin)
    sign_in_as @user
  end
  
  def test_should_get_index
    get :index
    assert_response :success
    assert_not_nil assigns(:holes)
  end

  def test_should_get_new
    get :new
    assert_response :success
  end

  def test_should_create_hole
    assert_difference('Hole.count') do
      post :create, :hole => { }
    end

    assert_redirected_to hole_path(assigns(:hole))
  end

  def test_should_show_hole
    get :show, :id => holes(:one).id
    assert_response :success
  end

  def test_should_get_edit
    get :edit, :id => holes(:one).id
    assert_response :success
  end

  def test_should_update_hole
    put :update, :id => holes(:one).id, :hole => { }
    assert_redirected_to hole_path(assigns(:hole))
  end

  def test_should_destroy_hole
    assert_difference('Hole.count', -1) do
      delete :destroy, :id => holes(:one).id
    end

    assert_redirected_to holes_path
  end
end
