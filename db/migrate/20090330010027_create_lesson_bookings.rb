class CreateLessonBookings < ActiveRecord::Migration
  def self.up
    create_table :lesson_bookings do |t|
      t.string :name
      t.string :address
      t.string :city
      t.string :province_state
      t.string :country
      t.string :phone_number
      t.string :email
      t.boolean :lesson
      t.string :preferred_day
      t.string :preferred_time
      t.string :clinic_number
      t.string :golf_school_number

      t.timestamps
    end
  end

  def self.down
    drop_table :lesson_bookings
  end
end
