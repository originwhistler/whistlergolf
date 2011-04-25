class AddSignupTypeToContest < ActiveRecord::Migration
  def self.up
    add_column :contest_entries, :signuptype, :string
  end

  def self.down
    remove_column :contest_entries, :signuptype
  end
end
