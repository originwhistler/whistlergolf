module AuthenticatedTestHelper
  # Sets the current passholder in the session from the passholder fixtures.
  def login_as(passholder)
    @request.session[:passholder_id] = passholder ? passholders(passholder).id : nil
  end

  def authorize_as(passholder)
    @request.env["HTTP_AUTHORIZATION"] = passholder ? ActionController::HttpAuthentication::Basic.encode_credentials(passholders(passholder).login, 'monkey') : nil
  end
  
end
