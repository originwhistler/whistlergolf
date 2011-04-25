require 'test_helper'

class LessonBookingsControllerTest < ActionController::TestCase
  def test_should_get_index
    get :index
    assert_response :success
    assert_not_nil assigns(:lesson_bookings)
  end

  def test_should_get_new
    get :new
    assert_response :success
  end

  def test_should_create_lesson_booking
    assert_difference('LessonBooking.count') do
      post :create, :lesson_booking => { }
    end

    assert_redirected_to lesson_booking_path(assigns(:lesson_booking))
  end

  def test_should_show_lesson_booking
    get :show, :id => lesson_bookings(:one).id
    assert_response :success
  end

  def test_should_get_edit
    get :edit, :id => lesson_bookings(:one).id
    assert_response :success
  end

  def test_should_update_lesson_booking
    put :update, :id => lesson_bookings(:one).id, :lesson_booking => { }
    assert_redirected_to lesson_booking_path(assigns(:lesson_booking))
  end

  def test_should_destroy_lesson_booking
    assert_difference('LessonBooking.count', -1) do
      delete :destroy, :id => lesson_bookings(:one).id
    end

    assert_redirected_to lesson_bookings_path
  end
end
