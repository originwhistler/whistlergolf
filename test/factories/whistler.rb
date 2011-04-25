Factory.sequence :email do |n|
  "user#{n}@example.com"
end

Factory.define :admin, :class => 'user' do |user|
  user.email                 { Factory.next :email }
  user.password              { "password" }
  user.password_confirmation { "password" }
  user.email_confirmed       { true }
  user.admin                 { true }
end

Factory.define :player, :class => 'player' do |user|
  user.email                 { Factory.next :email }
  user.password              { "password" }
  user.password_confirmation { "password" }
  user.email_confirmed       { true }
end

Factory.define :passholder, :class => 'passholder' do |user|
  user.email                 { Factory.next :email }
  user.password              { "password" }
  user.password_confirmation { "password" }
  user.email_confirmed       { true }
end
