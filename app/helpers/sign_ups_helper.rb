module SignUpsHelper
  def province_state_options
    '
    <option selected>Select your Province or State
    <option></option>
    <option>     PROVINCES</option>
    <option value="AB">Alberta</option>

    <option value="BC">British Columbia</option>
    <option value="MB">Manitoba</option>
    <option value="NB">New Brunswick</option>
    <option value="NF">New Foundland</option>
    <option value="NT">Northwest Territories</option>
    <option value="NS">Nova Scotia</option>
    <option value="ON">Ontario</option>
    <option value="PI">Prince Edward Island</option>
    <option value="PQ">Quebec</option>

    <option value="SA">Saskatchewan</option>
    <option value="YT">Yukon Territory</option>

    <option>    STATES</option>
    <option value="AL">Alabama</option>

    <option value="AK">Alaska</option>
    <option value="AZ">Arizona</option>
    <option value="AR">Arkansas</option>
    <option value="CA">California</option>
    <option value="CO">Colorado</option>
    <option value="CT">Connecticut</option>
    <option value="DE">Delaware</option>
    <option value="DC">District of Columbia</option>
    <option value="FL">Florida</option>

    <option value="GA">Georgia</option>
    <option value="HI">Hawaii</option>
    <option value="ID">Idaho</option>
    <option value="IL">Illinois</option>
    <option value="IN">Indiana</option>
    <option value="IA">Iowa</option>
    <option value="KS">Kansas</option>
    <option value="KY">Kentucky</option>
    <option value="LA">Louisiana</option>

    <option value="ME">Maine</option>
    <option value="MD">Maryland</option>
    <option value="MA">Massachusetts</option>
    <option value="MI">Michigan</option>
    <option value="MN">Minnesota</option>
    <option value="MS">Mississippi</option>
    <option value="MO">Missouri</option>
    <option value="MT">Montana</option>
    <option value="NE">Nebraska</option>

    <option value="NV">Nevada</option>
    <option value="NH">New Hampshire</option>
    <option value="NJ">New Jersey</option>
    <option value="NM">New Mexico</option>
    <option value="NY">New York</option>
    <option value="NC">North Carolina</option>
    <option value="ND">North Dakota</option>
    <option value="OH">Ohio</option>
    <option value="OK">Oklahoma</option>

    <option value="OR">Oregon</option>
    <option value="PA">Pennsylvania</option>
    <option value="RI">Rhode Island</option>
    <option value="SC">South Carolina</option>
    <option value="SD">South Dakota</option>
    <option value="TN">Tennessee</option>
    <option value="TX">Texas</option>
    <option value="UT">Utah</option>
    <option value="VT">Vermont</option>

    <option value="VA">Virginia</option>
    <option value="WA">Washington</option>
    <option value="DC">Washington D.C.</option>
    <option value="WV">West Virginia</option>
    <option value="WI">Wisconsin</option>
    <option value="WY">Wyoming</option>
    <option>

    '
  end
  
  def influences
    [ ['Price'], ['Playability and layout of course'], ['Service experience'], ['Designer'], ['Course conditioning'], ['Location of course to where you live'], ['Food and Beverage experience'], ['Quality of Practice Facility'], ['Quality of service and selection in golf shop'], ['Overall experience'] ]
  end
end
