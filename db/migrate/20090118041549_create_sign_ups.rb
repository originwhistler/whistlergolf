class CreateSignUps < ActiveRecord::Migration
  def self.up
    create_table :sign_ups do |t|
      t.string :first_name
      t.string :last_name
      t.string :street_address
      t.string :city
      t.string :province
      t.string :postal_code
      t.string :telephone
      t.string :email
      t.string :type

      t.timestamps
    end
  end

  def self.down
    drop_table :sign_ups
  end
end
