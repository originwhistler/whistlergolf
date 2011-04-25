class GroupOutingsController < ApplicationController
  
  # layout 'admin', :except => :new
  before_filter :admin_only, :except => ['new', 'create', 'show']
  
  
  # GET /group_outings
  # GET /group_outings.xml
  def index
    @group_outings = GroupOuting.find(:all)

    respond_to do |format|
      format.html # index.html.erb
      format.xml  { render :xml => @group_outings }
      format.csv { send_data @group_outings.to_csv }
      
    end
  end

  # GET /group_outings/1
  # GET /group_outings/1.xml
  def show
    @group_outing = GroupOuting.find(params[:id])

    respond_to do |format|
      format.html # show.html.erb
      format.xml  { render :xml => @group_outing }
      format.csv { send_data [@group_outing].to_csv }
      
    end
  end

  # GET /group_outings/new
  # GET /group_outings/new.xml
  def new
    @group_outing = GroupOuting.new

    respond_to do |format|
      format.html # new.html.erb
      format.xml  { render :xml => @group_outing }
    end
  end

  # GET /group_outings/1/edit
  def edit
    @group_outing = GroupOuting.find(params[:id])
  end

  # POST /group_outings
  # POST /group_outings.xml
  def create
    @group_outing = GroupOuting.new(params[:group_outing])

    respond_to do |format|
      if @group_outing.save
        flash[:notice] = 'Thank you, your Group Outing Request was submitted successfully'
        format.html { redirect_to @group_outing }
        format.xml  { render :xml => @group_outing, :status => :created, :location => @group_outing }
      else
        format.html { render :action => "new" }
        format.xml  { render :xml => @group_outing.errors, :status => :unprocessable_entity }
      end
    end
  end

  # PUT /group_outings/1
  # PUT /group_outings/1.xml
  def update
    @group_outing = GroupOuting.find(params[:id])

    respond_to do |format|
      if @group_outing.update_attributes(params[:group_outing])
        flash[:notice] = 'GroupOuting was successfully updated.'
        format.html { redirect_to(@group_outing) }
        format.xml  { head :ok }
      else
        format.html { render :action => "edit" }
        format.xml  { render :xml => @group_outing.errors, :status => :unprocessable_entity }
      end
    end
  end

  # DELETE /group_outings/1
  # DELETE /group_outings/1.xml
  def destroy
    @group_outing = GroupOuting.find(params[:id])
    @group_outing.destroy

    respond_to do |format|
      format.html { redirect_to(group_outings_url) }
      format.xml  { head :ok }
    end
  end
end
