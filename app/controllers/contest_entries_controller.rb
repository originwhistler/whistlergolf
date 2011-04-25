class ContestEntriesController < ApplicationController
  
  before_filter :admin_only, :except => ['new', 'create']
  
  
  # GET /contest_entries
  # GET /contest_entries.xml
  def index
    @contest_entries = ContestEntry.find(:all)

    respond_to do |format|
      format.html # index.html.erb
      format.xml  { render :xml => @contest_entries }
      format.csv { send_data @contest_entries.to_csv }
    end
    
  end

  # GET /contest_entries/1
  # GET /contest_entries/1.xml
  def show
    @contest_entry = ContestEntry.find(params[:id])

    respond_to do |format|
      format.html # show.html.erb
      format.xml  { render :xml => @contest_entry }
    end
  end

  # GET /contest_entries/new
  # GET /contest_entries/new.xml
  def new
    @contest_entry = ContestEntry.new

    respond_to do |format|
      format.html # new.html.erb
      format.xml  { render :xml => @contest_entry }
    end
  end

  # GET /contest_entries/1/edit
  def edit
    @contest_entry = ContestEntry.find(params[:id])
  end

  # POST /contest_entries
  # POST /contest_entries.xml
  def create
    @contest_entry = ContestEntry.new(params[:contest_entry])

    respond_to do |format|
      if @contest_entry.save
        flash[:notice] = 'Thank You, Your Contest Entry was successfully submitted.'
        format.html { redirect_to new_contest_entry_path }
        format.xml  { render :xml => @contest_entry, :status => :created, :location => @contest_entry }
      else
        format.html { render :action => "new" }
        format.xml  { render :xml => @contest_entry.errors, :status => :unprocessable_entity }
      end
    end
  end

  # PUT /contest_entries/1
  # PUT /contest_entries/1.xml
  def update
    @contest_entry = ContestEntry.find(params[:id])

    respond_to do |format|
      if @contest_entry.update_attributes(params[:contest_entry])
        flash[:notice] = 'ContestEntry was successfully updated.'
        format.html { redirect_to(@contest_entry) }
        format.xml  { head :ok }
      else
        format.html { render :action => "edit" }
        format.xml  { render :xml => @contest_entry.errors, :status => :unprocessable_entity }
      end
    end
  end

  # DELETE /contest_entries/1
  # DELETE /contest_entries/1.xml
  def destroy
    @contest_entry = ContestEntry.find(params[:id])
    @contest_entry.destroy

    respond_to do |format|
      format.html { redirect_to(contest_entries_url) }
      format.xml  { head :ok }
    end
  end
end
