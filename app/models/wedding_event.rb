class WeddingEvent < ActiveRecord::Base
  def after_create
    Mailer.deliver_wedding_event(self)  unless TEST_MODE
    Mailer.deliver_wedding_event_confirmation(self)  unless TEST_MODE
  end
end
