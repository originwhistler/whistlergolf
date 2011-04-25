class LessonBooking < ActiveRecord::Base
  
  validates_presence_of :participant_name, :email, :phone_number, :address, :city, :province_state
  
  def after_create
    Mailer.deliver_lesson_booking(self)  unless TEST_MODE
    Mailer.deliver_lesson_booking_confirmation(self)  unless TEST_MODE
  end
end
