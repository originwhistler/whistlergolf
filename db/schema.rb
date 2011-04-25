# This file is auto-generated from the current state of the database. Instead of editing this file, 
# please use the migrations feature of Active Record to incrementally modify your database, and
# then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your database schema. If you need
# to create the application database on another system, you should be using db:schema:load, not running
# all the migrations from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended to check this file into your version control system.

ActiveRecord::Schema.define(:version => 20090522051940) do

  create_table "blog_categories", :force => true do |t|
    t.string   "title"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "blog_posts", :force => true do |t|
    t.integer  "blog_category_id", :limit => 11
    t.string   "title"
    t.date     "date"
    t.text     "content"
    t.boolean  "active"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "contacts", :force => true do |t|
    t.string   "first_name"
    t.string   "last_name"
    t.string   "street_address"
    t.string   "city"
    t.string   "province"
    t.string   "postal_code"
    t.string   "telephone"
    t.string   "email"
    t.text     "comments"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "contents", :force => true do |t|
    t.string   "title"
    t.text     "text"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "attachment_file_name"
    t.string   "attachment_content_type"
    t.integer  "attachment_file_size",    :limit => 11
    t.datetime "attachment_updated_at"
  end

  create_table "contest_entries", :force => true do |t|
    t.string   "first_name"
    t.string   "last_name"
    t.string   "telephone"
    t.string   "email"
    t.boolean  "group_organizer"
    t.boolean  "large_group"
    t.text     "comments"
    t.boolean  "agree_to_terms"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "signuptype"
    t.string   "promotions"
  end

  create_table "group_outings", :force => true do |t|
    t.string   "first_name"
    t.string   "last_name"
    t.string   "email_type"
    t.string   "email"
    t.string   "phone_type"
    t.string   "area_code"
    t.string   "phone_number"
    t.string   "country"
    t.string   "address_type"
    t.string   "street_1"
    t.string   "street_2"
    t.string   "city"
    t.string   "province"
    t.string   "postal_code"
    t.string   "country_address"
    t.string   "event_length"
    t.string   "number_of_attendees"
    t.boolean  "held_event_before"
    t.boolean  "decision_maker"
    t.string   "contact_method"
    t.string   "referred_by"
    t.text     "comments"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "holes", :force => true do |t|
    t.integer  "number",             :limit => 11
    t.integer  "par",                :limit => 11
    t.integer  "tournament_tees",    :limit => 11
    t.integer  "palmer_tees",        :limit => 11
    t.integer  "white_tees",         :limit => 11
    t.integer  "forward_tees",       :limit => 11
    t.integer  "handicap_mens",      :limit => 11
    t.integer  "handicap_womens",    :limit => 11
    t.text     "description"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "image_file_name"
    t.string   "image_content_type"
    t.integer  "image_file_size",    :limit => 11
    t.string   "name"
  end

  create_table "lesson_bookings", :force => true do |t|
    t.string   "name"
    t.string   "address"
    t.string   "city"
    t.string   "province_state"
    t.string   "country"
    t.string   "phone_number"
    t.string   "email"
    t.boolean  "lesson"
    t.string   "preferred_day"
    t.string   "preferred_time"
    t.string   "clinic_number"
    t.string   "golf_school_number"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "passholders", :force => true do |t|
    t.string   "login",                     :limit => 40
    t.string   "name",                      :limit => 100, :default => ""
    t.string   "email",                     :limit => 100
    t.string   "crypted_password",          :limit => 40
    t.string   "salt",                      :limit => 40
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "remember_token",            :limit => 40
    t.datetime "remember_token_expires_at"
    t.string   "activation_code",           :limit => 40
    t.datetime "activated_at"
    t.string   "state",                                    :default => "passive"
    t.datetime "deleted_at"
  end

  add_index "passholders", ["login"], :name => "index_passholders_on_login", :unique => true

  create_table "players", :force => true do |t|
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "sign_ups", :force => true do |t|
    t.string   "first_name"
    t.string   "last_name"
    t.string   "street_address"
    t.string   "city"
    t.string   "province"
    t.string   "postal_code"
    t.string   "telephone"
    t.string   "email"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "signuptype"
    t.string   "handicap"
    t.string   "timeperyear"
    t.string   "company_event"
    t.date     "birthdate"
    t.text     "promotions"
    t.string   "income"
    t.text     "other_courses"
    t.string   "number_of_lessons"
    t.string   "times_per_month"
    t.string   "how_did_you_hear"
    t.string   "website_rating"
    t.string   "influencing_factor"
    t.string   "influencing_factor_2"
    t.string   "influencing_factor_3"
    t.string   "company_name"
  end

  create_table "users", :force => true do |t|
    t.string   "login",                     :limit => 40
    t.string   "name",                      :limit => 100, :default => ""
    t.string   "email",                     :limit => 100
    t.string   "crypted_password",          :limit => 40
    t.string   "salt",                      :limit => 40
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "remember_token",            :limit => 40
    t.datetime "remember_token_expires_at"
    t.string   "encrypted_password",        :limit => 128
    t.string   "token",                     :limit => 128
    t.datetime "token_expires_at"
    t.boolean  "email_confirmed",                          :default => false, :null => false
    t.string   "type"
    t.boolean  "admin"
  end

  add_index "users", ["login"], :name => "index_users_on_login", :unique => true
  add_index "users", ["id", "token"], :name => "index_users_on_id_and_token"
  add_index "users", ["email"], :name => "index_users_on_email"
  add_index "users", ["token"], :name => "index_users_on_token"

  create_table "wedding_events", :force => true do |t|
    t.string   "first_name"
    t.string   "last_name"
    t.string   "gender"
    t.string   "email_type"
    t.string   "email"
    t.string   "address_type"
    t.string   "street_1"
    t.string   "street_2"
    t.string   "city"
    t.string   "province"
    t.string   "country"
    t.string   "phone_type"
    t.string   "area_code"
    t.string   "phone_number"
    t.string   "country_phone"
    t.date     "requested_date"
    t.date     "alternate_date"
    t.string   "contact_time"
    t.text     "comments"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

end
