class ConfirmationsController < ApplicationController
  include Clearance::App::Controllers::ConfirmationsController
  
  def forbid_confirmed_user
    user = User.find_by_id(params[:user_id])
    if user && user.email_confirmed?
      flash[:notice] = "You have already confirmed your membership. Thank you."
      redirect_to user
      # raise ActionController::Forbidden, "confirmed user"
    end
  end
  
  
end
