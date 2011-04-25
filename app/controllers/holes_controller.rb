class HolesController < ApplicationController
  
  # layout 'admin', :except => :new
  before_filter :admin_only, :except => ['show', 'index']
  
  # GET /holes
  # GET /holes.xml
  def index
    @holes = Hole.find(:all, :order => :number)

    respond_to do |format|
      format.html # index.html.erb
      format.xml  { render :xml => @holes }
    end
  end

  # GET /holes/1
  # GET /holes/1.xml
  def show
    @hole = Hole.find(params[:id])

    respond_to do |format|
      format.html # show.html.erb
      format.xml  { render :xml => @hole }
    end
  end

  # GET /holes/new
  # GET /holes/new.xml
  def new
    @hole = Hole.new

    respond_to do |format|
      format.html # new.html.erb
      format.xml  { render :xml => @hole }
    end
  end

  # GET /holes/1/edit
  def edit
    @hole = Hole.find(params[:id])
  end

  # POST /holes
  # POST /holes.xml
  def create
    @hole = Hole.new(params[:hole])

    respond_to do |format|
      if @hole.save
        flash[:notice] = 'Hole was successfully created.'
        format.html { redirect_to(@hole) }
        format.xml  { render :xml => @hole, :status => :created, :location => @hole }
      else
        format.html { render :action => "new" }
        format.xml  { render :xml => @hole.errors, :status => :unprocessable_entity }
      end
    end
  end

  # PUT /holes/1
  # PUT /holes/1.xml
  def update
    @hole = Hole.find(params[:id])

    respond_to do |format|
      if @hole.update_attributes(params[:hole])
        flash[:notice] = 'Hole was successfully updated.'
        format.html { redirect_to(@hole) }
        format.xml  { head :ok }
      else
        format.html { render :action => "edit" }
        format.xml  { render :xml => @hole.errors, :status => :unprocessable_entity }
      end
    end
  end

  # DELETE /holes/1
  # DELETE /holes/1.xml
  def destroy
    @hole = Hole.find(params[:id])
    @hole.destroy

    respond_to do |format|
      format.html { redirect_to(holes_url) }
      format.xml  { head :ok }
    end
  end
end
