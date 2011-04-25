class AddPromosToContest < ActiveRecord::Migration
  def self.up
    add_column :contest_entries, :promotions, :string
  end

  def self.down
    remove_column :contest_entries, :promotions
  end
end
