require 'test_helper'

class SignUpsControllerTest < ActionController::TestCase
  fixtures :all
  def setup
    @user = Factory(:admin)
    sign_in_as @user
  end
  
  def test_should_get_index
    get :index
    assert_response :success
    assert_not_nil assigns(:sign_ups)
  end

  def test_should_get_new
    get :new
    assert_response :success
  end

  def test_should_create_sign_up
    assert_difference('SignUp.count') do
      post :create, :sign_up => { }
    end

    assert_redirected_to sign_up_path(assigns(:sign_up))
  end

  def test_should_show_sign_up
    get :show, :id => sign_ups(:one).id
    assert_response :success
  end

  def test_should_get_edit
    get :edit, :id => sign_ups(:one).id
    assert_response :success
  end

  def test_should_update_sign_up
    put :update, :id => sign_ups(:one).id, :sign_up => { }
    assert_redirected_to sign_up_path(assigns(:sign_up))
  end

  def test_should_destroy_sign_up
    assert_difference('SignUp.count', -1) do
      delete :destroy, :id => sign_ups(:one).id
    end

    assert_redirected_to sign_ups_path
  end
end
