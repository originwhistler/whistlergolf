# Be sure to restart your server when you modify this file

# Uncomment below to force Rails into production mode when
# you don't control web/app server and can't set it the proper way
# ENV['RAILS_ENV'] ||= 'production'

# Specifies gem version of Rails to use when vendor/rails is not present
RAILS_GEM_VERSION = '2.1.0' unless defined? RAILS_GEM_VERSION



# Bootstrap the Rails environment, frameworks, and default configuration
require File.join(File.dirname(__FILE__), 'boot')

Rails::Initializer.run do |config|
  # Settings in config/environments/* take precedence over those specified here.
  # Application configuration should go into files in config/initializers
  # -- all .rb files in that directory are automatically loaded.
  # See Rails::Configuration for more options.

  # Skip frameworks you're not going to use. To use Rails without a database
  # you must remove the Active Record framework.
  # config.frameworks -= [ :active_record, :active_resource, :action_mailer ]

  # Specify gems that this application depends on. 
  # They can then be installed with "rake gems:install" on new installations.
  # config.gem "bj"
  # config.gem "hpricot", :version => '0.6', :source => "http://code.whytheluckystiff.net"
  # config.gem "aws-s3", :lib => "aws/s3"
  # config.gem "thoughtbot-shoulda", :source => "http://gems.github.com"
  # config.gem "patientslikeme-campaign_monitor-1.3.0", :source => "http://gems.github.com"

  # config.gem "thoughtbot-clearance", 
  #   :lib     => 'clearance', 
  #   :source  => 'http://gems.github.com', 
  #   :version => '>= 0.5.0'


  # Only load the plugins named here, in the order given. By default, all plugins 
  # in vendor/plugins are loaded in alphabetical order.
  # :all can be used as a placeholder for all plugins not explicitly named
  # config.plugins = [ :exception_notification, :ssl_requirement, :all ]

  # Add additional load paths for your own custom dirs
  # config.load_paths += %W( #{RAILS_ROOT}/vendor/gems )
  # config.load_paths += Dir["#{RAILS_ROOT}/vendor/gems/**"].map do |dir| 
  #   File.directory?(lib = "#{dir}/lib") ? lib : dir
  # end
  

  # Force all environments to use the same logger level
  # (by default production uses :info, the others :debug)
  # config.log_level = :debug

  # Make Time.zone default to the specified zone, and make Active Record store time values
  # in the database in UTC, and return them converted to the specified local zone.
  # Run "rake -D time" for a list of tasks for finding time zone names. Uncomment to use default local time.
  config.time_zone = 'UTC'
  

  # Your secret key for verifying cookie session data integrity.
  # If you change this key, all old sessions will become invalid!
  # Make sure the secret is at least 30 characters and all random, 
  # no regular words or you'll be exposed to dictionary attacks.
  config.action_controller.session = {
    :session_key => '_whistler_session',
    :secret      => 'b0b11a0bc2cb073ee56a4261c619c9992aabf7219c52e5865182140e0d623bfa1cae8c1863820b1b6a43b71318635c43b6748ff6535f69f59e756dc95f2e61f0'
  }

  # Use the database for sessions instead of the cookie-based default,
  # which shouldn't be used to store highly confidential information
  # (create the session table with "rake db:sessions:create")
  # config.action_controller.session_store = :active_record_store

  # Use SQL instead of Active Record's schema dumper when creating the test database.
  # This is necessary if your schema can't be completely dumped by the schema dumper,
  # like if you have constraints or database-specific column types
  # config.active_record.schema_format = :sql

  # Activate observers that should always be running
  # config.active_record.observers = :cacher, :garbage_collector
end

  
# Campaign Monitor
CAMPAIGN_MONITOR_API_KEY = 'bb052f38ce413e32d0599c2adde7caaf'

BOOKING_URL = 'http://www.golfswitch.com'

WHISTLER_URL = 'http://whistlergolf.com'

WHISTLER_EMAIL = 'web@origindesign.ca'

SITE_DOMAIN = 'whistlergolf.com'

ExceptionNotifier.exception_recipients = %w(web@origindesign.ca rory@origindesign.ca)


# ACTION MAILER ENVIRONMENT
# -----------------------------------------
ActionMailer::Base.delivery_method = :sendmail

# ActionMailer::Base.smtp_settings  = {
#   :address  => "mail.calgpetrolcurling.ab.ca",
#   :port   => 25,
#   :domain   => "calgpetrolcurling.ab.ca",
#   :authentication => :login,
#   :user_name  => "cpcc",
#   :password => "cpcc"
# }
ActionMailer::Base.perform_deliveries = true
# ActionMailer::Base.raise_delivery_errors = true
ActionMailer::Base.default_charset = "utf-8"