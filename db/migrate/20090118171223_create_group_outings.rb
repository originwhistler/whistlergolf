class CreateGroupOutings < ActiveRecord::Migration
  def self.up
    create_table :group_outings do |t|
      t.string :first_name
      t.string :last_name
      t.string :email_type
      t.string :email
      t.string :phone_type
      t.string :area_code
      t.string :phone_number
      t.string :country
      t.string :address_type
      t.string :street_1
      t.string :street_2
      t.string :city
      t.string :province
      t.string :postal_code
      t.string :country_address
      t.string :event_length
      t.string :number_of_attendees
      t.boolean :held_event_before
      t.boolean :decision_maker
      t.string :contact_method
      t.string :referred_by
      t.text :comments

      t.timestamps
    end
  end

  def self.down
    drop_table :group_outings
  end
end
