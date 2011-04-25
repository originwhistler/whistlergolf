class Player < User
  def after_create
    confirm_email!
    Mailer.deliver_player_confirmation(self)  unless TEST_MODE
  end
end
