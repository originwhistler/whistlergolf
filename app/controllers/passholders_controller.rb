class PassholdersController < UsersController
  
  before_filter :get_passholder, :only => ['index', 'edit', 'update']
  before_filter :passholders_only
  
  
  def index
    if @passholder
      redirect_to passholder_path(@passholder) 
    elsif signed_in_as_admin?
      flash[:notice] = 'Need to pick a passholder.'
      redirect_to '/admin'
    else
      flash[:notice] = 'Must be logged in.'
      redirect_to '/'
    end
  end
  
  def show
    @passholder = Passholder.find(params[:id])
    @specials = Content.find_by_title('Passholders Specials') || ''
    @events = Content.find_by_title('Passholders Upcoming Events') || ''
    @info = Content.find_by_title('Passholders Other Information') || ''
  end
  
  def new
    @passholder = Passholder.new
  end
  
  def create
    @passholder = Passholder.create(params[:passholder])
    if @passholder.save
      # ClearanceMailer.deliver_confirmation @passholder
      flash[:notice] = "Passholder created successfully"
      redirect_to @passholder
    else
      flash[:notice] = "Sorry there was an issue."
      render :action => "new"
    end
  end
  
  
  protected
    def get_passholder
      if signed_in_as_admin?
        @passholder = Passholder.find(params[:id]) if params[:id]
      elsif signed_in?
        @passholder = current_user
      end
    end
  
end
