class UsersController < ApplicationController
  include Clearance::App::Controllers::UsersController
  
  def login_status
    "Logged in as: #{current_user.email}" if current_user
  end
  
  def edit
    @user = User.find(params[:id])
    render :template => 'users/edit'
  end
  
  def destroy
    @user = User.find(params[:id])
    @user.destroy

    respond_to do |format|
      flash[:notice] = "Successfully deleted."
      format.html { redirect_to(admin_url) }
      format.xml  { head :ok }
    end
  end
  
end