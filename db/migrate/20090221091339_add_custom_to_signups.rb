class AddCustomToSignups < ActiveRecord::Migration
  def self.up
    remove_column :sign_ups, :type
    add_column :sign_ups, :signuptype, :string
    add_column :sign_ups, :handicap, :string
    add_column :sign_ups, :timeperyear, :string
    add_column :sign_ups, :company_event, :string
    add_column :sign_ups, :birthdate, :date
    add_column :sign_ups, :promotions, :boolean

  end

  def self.down
    remove_column :sign_ups, :signuptype
    remove_column :sign_ups, :handicap
    remove_column :sign_ups, :timeperyear
    remove_column :sign_ups, :company_event
    remove_column :sign_ups, :birthdate
    remove_column :sign_ups, :promotions
  end
end
