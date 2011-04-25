class GroupOuting < ActiveRecord::Base
  def after_create
    Mailer.deliver_group_outing(self)  unless TEST_MODE
    Mailer.deliver_group_outing_confirmation(self)  unless TEST_MODE
  end
end
