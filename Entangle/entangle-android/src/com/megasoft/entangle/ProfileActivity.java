package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import com.megasoft.requests.GetRequest;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

public class ProfileActivity extends Activity {
	private TextView name;
	private TextView descr;
	private TextView balance;
	private TextView birthdate;
	private ImageView verifiedView;
	final Activity self = this;
    LinearLayout layoutContainer;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_profile);
			
				try {

					viewProfile();
				} catch (JSONException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}
	
	public void viewProfile() throws JSONException {
		name = (TextView) findViewById(R.id.nameView);
		descr = (TextView) findViewById(R.id.descriptionView);
		balance = (TextView) findViewById(R.id.balanceView);
		birthdate = (TextView) findViewById(R.id.birthdateView);
		verifiedView = (ImageView) findViewById(R.id.verifiedView);
		Intent intent = getIntent();
		layoutContainer = (LinearLayout) this.findViewById(R.id.layout_container);
		int tangleId = intent.getIntExtra("tangle id", -1);
		int userId = intent.getIntExtra("user id", -1);

		GetRequest request = new GetRequest("http://entangle2.apiary-mock.com/tangle/" + tangleId + "/user/" + userId + "/profile") {
			
			protected void onPostExecute(String response) {  // On Post execute means after the execution of the request ( the callback )
                     try {
						JSONObject jSon = new JSONObject(response);
						//boolean loggedIn = jSon.getBoolean("loggedIn");
						
						JSONObject info =  jSon.getJSONObject("information");
						name.setText(info.getString("name"));
						descr.setText("Description: " + info.getString("Description"));
						balance.setText("Credit: " + info.getString("balance") + " points");
						birthdate.setText("Birthdate: " + info.getString("birthdate"));
						boolean verified = info.getBoolean("verified");
						if (verified) {
							verifiedView.setVisibility(1);
						} else {
							verifiedView.setVisibility(0);
						}
						JSONArray array = jSon.getJSONArray("transactions");
						for(int i = 0; i < array.length(); i++) { 
							JSONObject object = array.getJSONObject(i);
							TextView transaction = new TextView(self);
							String requester = object.getString("requesterName");
							String request = object.getString("requestDescription");
							String amount = object.getString("amount");
							transaction.setText("Requester: " + requester 
									+ '\n' + "Request: " + request
									+ '\n' + "Amount: " + amount);
							layoutContainer.addView(transaction);
						}
                     } catch (JSONException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
                  }
		};
		request.execute();
	}
	

}
