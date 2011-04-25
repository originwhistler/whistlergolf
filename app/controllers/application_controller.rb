# Filters added to this controller apply to all controllers in the application.
# Likewise, all the methods added will be available for all controllers.
require 'campaign_monitor'
require 'exportable'
class ApplicationController < ActionController::Base
  include Clearance::App::Controllers::ApplicationController
  helper :all # include all helpers, all the time
  # Be sure to include AuthenticationSystem in Application Controller instead
  include AuthenticatedSystem
  include ExceptionNotifiable
  
  # See ActionController::RequestForgeryProtection for details
  # Uncomment the :secret if you're not using the cookie session store
  # protect_from_forgery # :secret => '42b80da51025e940c27a479d95de3a22'
  
  # See ActionController::Base for details 
  # Uncomment this to filter the contents of submitted sensitive data parameters
  # from your application log (in this case, all fields with names like "password"). 
  # filter_parameter_logging :password
  
  helper_method :signed_in_as_admin?

    def signed_in_as_admin?
      signed_in? && current_user.admin?
    end
    
    def is_passholder?
      current_user.is_a?(Passholder)
    end

    def players_only
      deny_access("Please Login As A Player or Create an Account to Access that Feature.") unless signed_in?
    end

    def passholders_only
      deny_access("Please Login As A Passholder to Access that Feature.") unless signed_in? && is_passholder? || signed_in_as_admin?
    end

    def admin_only
      deny_access("Please Login as an administrator to Access that Feature.") unless signed_in_as_admin?
    end
  
  
end
