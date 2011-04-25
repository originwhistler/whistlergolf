class AddEmailReplyContent < ActiveRecord::Migration
  def self.up
    Content.create(:title => 'Group Outing Confirmation Email', :text => "
    
    
    <p>Thank you for requesting information on Golf Group Outings at the Whistler Golf Club. Our Sales team will be in touch shortly to discuss your request. Did you know:</p>
    <ul>
    <li>The Whistler Golf Club is the leading golf group outing facility in Whistler.</li>
    <li>We are perfectly located in the heart of Whistler Village within walking distance from most hotels.</li>
    <li>We have access to the best hotel rates in Whistler.</li>
    <li>We are the 27th ranked public/resort course in Canada, according to the Globe and Mail Golf rankings.</li>
    <li>In our guest survey, 100% of our group guests said they would recommend our facility to others.</li>
    <li>We can custom build any food and beverage menu or prize table to fit your needs.</li>
    </ul>

    <blockquote><span class='open_quote'/>I wish all golf courses were like yours! Thanks for a great couple of days!<span class='close_quote'/><cite><b>ADP</b> (hosted 6 consecutive incentive shotguns for North American sales leaders)</cite></blockquote>
    <h3>ASK HOW YOU CAN GET A SLEEVE OF BALLS PER PLAYER AND A BUFFET FOR ONLY $10 PER PLAYER!!</h3>


    <p>
    Sincerley <br>
    Ro Davies<br>
    SALES MANAGER<br>
    604-938-5886<br>
    <a href='mailto:&#x72;&#x6F;&#x40;&#x77;&#x68;&#x69;&#x73;&#x74;&#x6C;&#x65;&#x72;&#x67;&#x6F;&#x6C;&#x66;&#x2E;&#x63;&#x6F;&#x6D;'>ro@whistlergolf.com</a>
    </p>


    
    ")
    Content.create(:title => 'Wedding Event Confirmation Email', :text => "
    
      
      <p>Thank you for requesting information on weddings at the Whistler Golf Club. Our Sales team will be in touch shortly to discuss your request. Did you know:</p>

      <ul>
      <li>We specialize in weddings from 50-150 people.</li>
      <li>We offer the best wedding value in Whistler.</li>
      <li>Our wedding knoll is situated in the heart of the golf course with 360-degree views of the surrounding peaks.</li>
      <li>We can customize a food and beverage menu to fit your budget.</li>
      </ul> 

       <blockquote><span class='open_quote'/>The beautiful scenery and excellent support staff made our wedding a dream come true.<span class='close_quote'/><cite><b>John and Dawn Scarth</b> </cite></blockquote>

      <h3>ASK ABOUT OUR FREE WEDDING VENUE OFFER!!</h3>


      <p>
      Sincerely<br>
      Suzy Downing<br>
      Wedding Specialist<br>
      604-938-5888<br>
      <a href='mailto:&#x73;&#x75;&#x7A;&#x79;&#x40;&#x77;&#x68;&#x69;&#x73;&#x74;&#x6C;&#x65;&#x72;&#x67;&#x6F;&#x6C;&#x66;&#x2E;&#x63;&#x6F;&#x6D;'>suzy@whistlergolf.com</a>
      </p>
    
    ")
    Content.create(:title => 'Player Confirmation', :text => "
    
    
    <p>Thank you for joining the Whistler Golf Club's Player's Club! Your Player's Club web site is www.whistlergolf.com/pc .  Click on “new user” to create your login and password.</p>

    <p>If you see a rate you like, book it as the rates are updated every 5 minutes and based on availability so we can't guarantee the great rate you see will stay for long! Special rates will continue to be available all season long, two weeks out from that day's date.  IF YOU WERE A MEMBER IN 2008 PLEASE USE YOUR LOGIN AND PASSWORD FROM 2008.  IF YOU CAN'T REMEMBER YOUR PASSWORD, CLICK ON 'I FORGOT MY PASSWORD'blo TO GET YOUR LOGIN AND PASSWORD.</p>

    <p>To check available times first select number of players. Then pick the day and approximate time you want to play and hit the appropriate search icon. From there, simply book your time online. ALL PLAYER'S MUST BE PLAYER'S CLUB MEMBERS TO RECEIVE THE MEMBER-ONLY RATES.  BC OR WASHINGTON STATE I.D. WILL BE REQURIED AT CHECK-IN.</p>

    <p>Have fun and we'll see you soon.</p>

    <p>
    Sincerely<br>
    Ro Davies<br>
    Sales Manager<br>
    604-938-5886
    </p>
    
    ")
  end

  def self.down
  end
end
