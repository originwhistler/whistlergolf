class CreateContacts < ActiveRecord::Migration
  def self.up
    create_table :contacts do |t|
      t.string :first_name
      t.string :last_name
      t.string :street_address
      t.string :city
      t.string :province
      t.string :postal_code
      t.string :telephone
      t.string :email
      t.text :comments

      t.timestamps
    end
  end

  def self.down
    drop_table :contacts
  end
end
