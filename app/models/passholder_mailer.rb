class PassholderMailer < ActionMailer::Base
  def signup_notification(passholder)
    setup_email(passholder)
    @subject    += 'Please activate your new account'
  
    @body[:url]  = "http://#{WHISTLER_URL}/activate/#{passholder.activation_code}"
  
  end
  
  def activation(passholder)
    setup_email(passholder)
    @subject    += 'Your account has been activated!'
    @body[:url]  = "http://YOURSITE/"
  end
  
  protected
    def setup_email(passholder)
      @recipients  = "#{passholder.email}"
      @from        = "admin@whistlergolf.com"
      @subject     = "Whistler Golf - Passholder Signup"
      @sent_on     = Time.now
      @body[:passholder] = passholder
    end
end
