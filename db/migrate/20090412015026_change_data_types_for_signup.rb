class ChangeDataTypesForSignup < ActiveRecord::Migration
  def self.up
    change_column(:sign_ups, :promotions, :text)
    change_column(:sign_ups, :other_courses, :text)
  end

  def self.down
    change_column(:sign_ups, :promotions, :string)
    change_column(:sign_ups, :other_courses, :string)
  end
end
