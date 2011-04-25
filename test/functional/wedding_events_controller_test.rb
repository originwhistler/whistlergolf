require 'test_helper'

class WeddingEventsControllerTest < ActionController::TestCase
  fixtures :all
  def setup
    @user = Factory(:admin)
    sign_in_as @user
  end
  
  def test_should_get_index
    get :index
    assert_response :success
    assert_not_nil assigns(:wedding_events)
  end

  def test_should_get_new
    get :new
    assert_response :success
  end

  def test_should_create_wedding_event
    assert_difference('WeddingEvent.count') do
      post :create, :wedding_event => { }
    end

    assert_redirected_to wedding_event_path(assigns(:wedding_event))
  end

  def test_should_show_wedding_event
    get :show, :id => wedding_events(:one).id
    assert_response :success
  end

  def test_should_get_edit
    get :edit, :id => wedding_events(:one).id
    assert_response :success
  end

  def test_should_update_wedding_event
    put :update, :id => wedding_events(:one).id, :wedding_event => { }
    assert_redirected_to wedding_event_path(assigns(:wedding_event))
  end

  def test_should_destroy_wedding_event
    assert_difference('WeddingEvent.count', -1) do
      delete :destroy, :id => wedding_events(:one).id
    end

    assert_redirected_to wedding_events_path
  end
end
