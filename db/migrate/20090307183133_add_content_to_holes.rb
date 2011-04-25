class AddContentToHoles < ActiveRecord::Migration
  def self.up

    	Hole.find(1).update_attributes(:number => 1,   :par => 4, :palmer_tees => 360, :tournament_tees => 380, :forward_tees => 340, :handicap_mens => 9 , :handicap_womens => 7,  :description => 'This hole favours a left to right tee shot. The hooked or pulled shot will leave you with a tough shot around the huge hemlock that stands left of this undulating green.')
    	Hole.find(2).update_attributes(:number => 2,   :par => 4, :palmer_tees => 410, :tournament_tees => 471, :forward_tees => 320, :handicap_mens => 3 , :handicap_womens => 11, :description => 'The tee shot landing area is guarded left and right by bunkers. Watch out for the large spruce on the right side 80 yards short of the green. Bunkers right and back protect this green.')
    	Hole.find(3).update_attributes(:number => 3,   :par => 5, :palmer_tees => 537, :tournament_tees => 552, :forward_tees => 466, :handicap_mens => 1 , :handicap_womens => 1,  :description => 'From tee to green, right is jail. Left center off the tee is ideal. From this point the hole doglegs sharply right around the corner. Don\'t miss this green long.')
    	Hole.find(4).update_attributes(:number => 4,   :par => 4, :palmer_tees => 301, :tournament_tees => 301, :forward_tees => 256, :handicap_mens => 17, :handicap_womens => 15, :description => 'This short par 4 will tempt many to drive the green, however the rise in front of the tee hides a narrow fairway and a creek down the entire right side.')
    	Hole.find(5).update_attributes(:number => 5,   :par => 3, :palmer_tees => 211, :tournament_tees => 233, :forward_tees => 166, :handicap_mens => 7 , :handicap_womens => 13, :description => 'The wind will play a big role in club selection as it is usually in your face. 5 usually requires a wood to reach the green which is no bargain itself. Stay below the hole!')
    	Hole.find(6).update_attributes(:number => 6,   :par => 4, :palmer_tees => 370, :tournament_tees => 370, :forward_tees => 332, :handicap_mens => 13, :handicap_womens => 5,  :description => 'The ideal position off the tee is right centre but be careful of the two fir trees. Beware of water behind the green as this hole usually plays downwind.')
    	Hole.find(7).update_attributes(:number => 7,   :par => 4, :palmer_tees => 363, :tournament_tees => 384, :forward_tees => 321, :handicap_mens => 11, :handicap_womens => 9,  :description => 'Put the driver away. Any tee shot to the right will catch the creek. Hit something about 200 yards at the left centre of the fairway. This will leave you about 140 yards in. On your second shot take enough club to clear right front trap. Another tough green. This is a classic short par 4.')
    	Hole.find(8).update_attributes(:number => 8,   :par => 3, :palmer_tees => 161, :tournament_tees => 188, :forward_tees => 119, :handicap_mens => 15, :handicap_womens => 17, :description => 'Trouble short and right. No kidding! Watch tier in centre of green. Do not be above the hole. I repeat, do not be above the hole.')
    	Hole.find(9).update_attributes(:number => 9,   :par => 5, :palmer_tees => 478, :tournament_tees => 527, :forward_tees => 417, :handicap_mens => 5 , :handicap_womens => 3,  :description => 'A straight away par 5. Crabapple creek guards the entire right side. If the wind is down and you rip a drive, eagle is a possibility.')
    	Hole.find(10).update_attributes(:number => 10, :par => 4, :palmer_tees => 326, :tournament_tees => 334, :forward_tees => 286, :handicap_mens => 14, :handicap_womens => 14, :description => 'Another classic short par 4. This dogleg right is guarded by a lake left and Crabapple creek which lines the right side and cuts in front of the green. Maybe the toughest second shot on the course. Take two more clubs when the wind is up.')
    	Hole.find(11).update_attributes(:number => 11, :par => 5, :palmer_tees => 486, :tournament_tees => 515, :forward_tees => 415, :handicap_mens => 8 , :handicap_womens => 2,  :description => 'Unless you kill a drive, going for the green in two isn\'t worth it. Aim left centre off the tee and hit an iron between the two creeks. This will leave you anywhere from 110-160 yards in. Four bunkers guard another tough green.')
    	Hole.find(12).update_attributes(:number => 12, :par => 4, :palmer_tees => 409, :tournament_tees => 431, :forward_tees => 328, :handicap_mens => 4 , :handicap_womens => 6,  :description => 'A left to right tee shot is required here. Check wind. When it\'s behind you the second shot plays much shorter than yardage.')
    	Hole.find(13).update_attributes(:number => 13, :par => 4, :palmer_tees => 391, :tournament_tees => 404, :forward_tees => 348, :handicap_mens => 6 , :handicap_womens => 4,  :description => 'Anything left of the tee is in bear country. Stay to the right side of the fairway off the tee for a better angle to the green. A beautiful view of Blackcomb Mountain awaits your second shot. Par is a great score.')
    	Hole.find(14).update_attributes(:number => 14, :par => 3, :palmer_tees => 160, :tournament_tees => 181, :forward_tees => 135, :handicap_mens => 18, :handicap_womens => 18, :description => 'This tee shot usually requires an extra club. Watch out for the tier that runs diagonally through the green.')
    	Hole.find(15).update_attributes(:number => 15, :par => 4, :palmer_tees => 361, :tournament_tees => 385, :forward_tees => 293, :handicap_mens => 12, :handicap_womens => 12, :description => 'Aim left centre here to avoid take left and creek and O.B. right. Again, wind in your face and a tier that runs right across the middle of the green. Check flag position.')
    	Hole.find(16).update_attributes(:number => 16, :par => 4, :palmer_tees => 450, :tournament_tees => 460, :forward_tees => 332, :handicap_mens => 2 , :handicap_womens => 8,  :description => 'What a view! A good tee shot gives you a chance to hit the green in two. Be careful, this green is very well guarded by water and sand.')
    	Hole.find(17).update_attributes(:number => 17, :par => 3, :palmer_tees => 169, :tournament_tees => 181, :forward_tees => 138, :handicap_mens => 16, :handicap_womens => 16, :description => 'This narrow green is guarded left and right by bunkers. Check flag position as this green is very long.	')
    	Hole.find(18).update_attributes(:number => 18, :par => 4, :palmer_tees => 393, :tournament_tees => 425, :forward_tees => 336, :handicap_mens => 10, :handicap_womens => 10, :description => 'A beautiful finishing hole with Mt Currie as your backdrop. A good tee shot is needed to avoid lake and fairway trap. Biggest green on course yields numerous three putts.')
    
  end

  def self.down
    
  end
end
