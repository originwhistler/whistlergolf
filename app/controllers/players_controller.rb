class PlayersController < UsersController

  before_filter :players_only, :except => ['index', 'new', 'create']
  before_filter :admin_only, :only => ['destroy']
  before_filter :get_player, :only => [ 'edit', 'update']
  
  def index
    if signed_in_as_admin?
      @players = Player.find(:all) 
    else
      redirect_to :action => 'new'
    end
  end
  
  def show
    @player = Player.find(params[:id])
  end
  
  def new
    @player = Player.new
    # render :template => 'sessions/new'
  end
  
  def create
    @player = Player.create(params[:player])
    if @player.save
      flash[:notice] = "You have successfully joined the Whistler Golf Club's Players Club"
      # render :text => "<p>Thank you for joining, you will receive an email shortly which to confirm your membership. You can <a href=''>login now by clicking here</a></p><p>If you don't receive an email, please check your junk mail.</p>", :layout => true
      redirect_to @player
    else
      flash[:notice] = "Sorry there was an issue."
      render :action => "new"
    end
  end
  
  def destroy
    @player = Player.find(params[:id])
    @player.destroy
    flash[:notice] = "Player deleted successfully"
    redirect_to admin_path
  end
  
  protected

  
    def get_player
      if signed_in_as_admin?
        @player = Player.find(params[:id]) if params[:id]
        @player ||= Player.all.last
      elsif signed_in?
        @player = current_user
      end
    end
  
end
