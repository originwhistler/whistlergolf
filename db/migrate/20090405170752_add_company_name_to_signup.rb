class AddCompanyNameToSignup < ActiveRecord::Migration
  def self.up
    add_column :sign_ups, :company_name, :string
  end

  def self.down
    remove_column :sign_ups, :company_name
  end
end
