require 'test_helper'

class GroupOutingsControllerTest < ActionController::TestCase
  fixtures :all
  def setup
    @user = Factory(:admin)
    sign_in_as @user
  end
  
  def test_should_get_index
    get :index
    assert_response :success
    assert_not_nil assigns(:group_outings)
  end

  def test_should_get_new
    get :new
    assert_response :success
  end

  def test_should_create_group_outing
    assert_difference('GroupOuting.count') do
      post :create, :group_outing => { }
    end

    assert_redirected_to group_outing_path(assigns(:group_outing))
  end

  def test_should_show_group_outing
    get :show, :id => group_outings(:one).id
    assert_response :success
  end

  def test_should_get_edit
    get :edit, :id => group_outings(:one).id
    assert_response :success
  end

  def test_should_update_group_outing
    put :update, :id => group_outings(:one).id, :group_outing => { }
    assert_redirected_to group_outing_path(assigns(:group_outing))
  end

  def test_should_destroy_group_outing
    assert_difference('GroupOuting.count', -1) do
      delete :destroy, :id => group_outings(:one).id
    end

    assert_redirected_to group_outings_path
  end
end
