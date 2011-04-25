class CreateWeddingEvents < ActiveRecord::Migration
  def self.up
    create_table :wedding_events do |t|
      t.string :first_name
      t.string :last_name
      t.string :gender
      t.string :email_type
      t.string :email
      t.string :address_type
      t.string :street_1
      t.string :street_2
      t.string :city
      t.string :province
      t.string :country
      t.string :phone_type
      t.string :area_code
      t.string :phone_number
      t.string :country_phone
      t.date :requested_date
      t.date :alternate_date
      t.string :contact_time
      t.text :comments

      t.timestamps
    end
  end

  def self.down
    drop_table :wedding_events
  end
end
