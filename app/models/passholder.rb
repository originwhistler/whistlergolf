class Passholder < User
  def after_create
    confirm_email!
    Mailer.deliver_passholder_confirmation(self) unless TEST_MODE
  end
end
