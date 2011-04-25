class WeddingEventsController < ApplicationController
  
  before_filter :admin_only, :except => ['new', 'create', 'show']
  
  
  
  # GET /wedding_events
  # GET /wedding_events.xml
  def index
    @wedding_events = WeddingEvent.find(:all)

    respond_to do |format|
      format.html # index.html.erb
      format.xml  { render :xml => @wedding_events }
      format.csv { send_data @wedding_events.to_csv }
      
    end
  end

  # GET /wedding_events/1
  # GET /wedding_events/1.xml
  def show
    @wedding_event = WeddingEvent.find(params[:id])

    respond_to do |format|
      format.html # show.html.erb
      format.xml  { render :xml => @wedding_event }
      format.csv { send_data [@wedding_event].to_csv }
      
    end
  end

  # GET /wedding_events/new
  # GET /wedding_events/new.xml
  def new
    @wedding_event = WeddingEvent.new

    respond_to do |format|
      format.html # new.html.erb
      format.xml  { render :xml => @wedding_event }
    end
  end

  # GET /wedding_events/1/edit
  def edit
    @wedding_event = WeddingEvent.find(params[:id])
  end

  # POST /wedding_events
  # POST /wedding_events.xml
  def create
    @wedding_event = WeddingEvent.new(params[:wedding_event])

    respond_to do |format|
      if @wedding_event.save
        flash[:notice] = 'Request was successfully created.'
        format.html { redirect_to @wedding_event }
        format.xml  { render :xml => @wedding_event, :status => :created, :location => @wedding_event }
      else
        format.html { render :action => "new" }
        format.xml  { render :xml => @wedding_event.errors, :status => :unprocessable_entity }
      end
    end
  end

  # PUT /wedding_events/1
  # PUT /wedding_events/1.xml
  def update
    @wedding_event = WeddingEvent.find(params[:id])

    respond_to do |format|
      if @wedding_event.update_attributes(params[:wedding_event])
        flash[:notice] = 'WeddingEvent was successfully updated.'
        format.html { redirect_to(@wedding_event) }
        format.xml  { head :ok }
      else
        format.html { render :action => "edit" }
        format.xml  { render :xml => @wedding_event.errors, :status => :unprocessable_entity }
      end
    end
  end

  # DELETE /wedding_events/1
  # DELETE /wedding_events/1.xml
  def destroy
    @wedding_event = WeddingEvent.find(params[:id])
    @wedding_event.destroy

    respond_to do |format|
      format.html { redirect_to(wedding_events_url) }
      format.xml  { head :ok }
    end
  end
end
