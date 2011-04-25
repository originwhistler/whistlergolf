class LessonBookingsController < ApplicationController
  
  before_filter :login_required, :except => [:new, :create, :show]
  
  # GET /lesson_bookings
  # GET /lesson_bookings.xml
  def index
    @lesson_bookings = LessonBooking.find(:all)

    respond_to do |format|
      format.html # index.html.erb
      format.xml  { render :xml => @lesson_bookings }
    end
  end

  # GET /lesson_bookings/1
  # GET /lesson_bookings/1.xml
  def show
    @lesson_booking = LessonBooking.find(params[:id])

    respond_to do |format|
      format.html # show.html.erb
      format.xml  { render :xml => @lesson_booking }
    end
  end

  # GET /lesson_bookings/new
  # GET /lesson_bookings/new.xml
  def new
    @lesson_booking = LessonBooking.new

    respond_to do |format|
      format.html # new.html.erb
      format.xml  { render :xml => @lesson_booking }
    end
  end

  # GET /lesson_bookings/1/edit
  def edit
    @lesson_booking = LessonBooking.find(params[:id])
  end

  # POST /lesson_bookings
  # POST /lesson_bookings.xml
  def create
    @lesson_booking = LessonBooking.new(params[:lesson_booking])

    respond_to do |format|
      if @lesson_booking.save
        flash[:notice] = 'Lesson Booking was successfully created.'
        format.html { redirect_to(@lesson_booking) }
        format.xml  { render :xml => @lesson_booking, :status => :created, :location => @lesson_booking }
      else
        format.html { render :action => "new" }
        format.xml  { render :xml => @lesson_booking.errors, :status => :unprocessable_entity }
      end
    end
  end

  # PUT /lesson_bookings/1
  # PUT /lesson_bookings/1.xml
  def update
    @lesson_booking = LessonBooking.find(params[:id])

    respond_to do |format|
      if @lesson_booking.update_attributes(params[:lesson_booking])
        flash[:notice] = 'Lesson Booking was successfully updated.'
        format.html { redirect_to(@lesson_booking) }
        format.xml  { head :ok }
      else
        format.html { render :action => "edit" }
        format.xml  { render :xml => @lesson_booking.errors, :status => :unprocessable_entity }
      end
    end
  end

  # DELETE /lesson_bookings/1
  # DELETE /lesson_bookings/1.xml
  def destroy
    @lesson_booking = LessonBooking.find(params[:id])
    @lesson_booking.destroy

    respond_to do |format|
      format.html { redirect_to(lesson_bookings_url) }
      format.xml  { head :ok }
    end
  end
end
