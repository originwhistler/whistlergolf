class PagesController < ApplicationController

  before_filter :admin_only, :only => ['edit', 'update', 'new', 'create', 'destroy']
  
  # def static
  #   render :template => '/pages/'+params[:path].join('/') and return if params[:path]
  # end

  def home
  end
   
  def contact
    @contact = Contact.new(params[:contact])
    if params[:contact]
      @contact.save!
      flash[:notice] = "Submission succeeded. Thank you for your time."      
    end
  end
   
  # layout :determine_layout
  # 
  # Last Resort For Page = Static Page view
  def method_missing(method)
    view_path = params[:path].join('/')
    view_path = 'index' if view_path.blank?
    render :template => '/pages/'+view_path
    cache_page
  end
  
  # Will Rescue Errors like 404
  def rescue_action(exception)
    @exception = exception
    render :template => 'pages/missing'
  end
  
  # protected  
  # 
  #   # Runs down the path (from end to start) looking for layout file
  #   def determine_layout 
  #       params[:path].reverse.each do |segment|
  #         @alternate_layout ||= segment if File.exist?(RAILS_ROOT+'/app/views/layouts/'+segment+'.html.haml')
  #         @alternate_layout ||= segment if File.exist?(RAILS_ROOT+'/app/views/layouts/'+segment+'.html.erb')
  #       end
  #       @alternate_layout.to_s || 'application'
  #   end
  # 

end
