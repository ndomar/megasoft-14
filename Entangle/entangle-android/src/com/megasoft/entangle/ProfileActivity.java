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

public class ProfileActivity extends Activity {
	private TextView name;
	private TextView descr;
	private TextView balance;
	private TextView birthdate;
	private ImageView verifiedView;
	final Activity self = this;
    final LinearLayout layoutContainer = (LinearLayout) this.findViewById(R.id.layout_container);

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
		int tangleId = intent.getIntExtra("tangle id", -1);
		int userId = intent.getIntExtra("user id", -1);
		GetRequest request = new GetRequest("http://entangle.io/user/" + userId + "/" + tangleId + "/profile") {
			 protected void onPostExecute(String response) {  // On Post execute means after the execution of the request ( the callback )
                     try {
						JSONObject jSon = new JSONObject(response);
						//boolean loggedIn = jSon.getBoolean("loggedIn");
						
						JSONArray info = jSon.getJSONArray("information");
						name.setText(info.getString(0));
						descr.setText("Description: " + info.getString(1));
						balance.setText("Credit: " + info.getString(2) + " points");
						birthdate.setText("Birthdate: " + info.getString(4));
						boolean verified = info.getBoolean(5);
						if (verified) {
							verifiedView.setVisibility(1);
						}
						JSONArray array = jSon.getJSONArray("Transactions");
						for(int i = 0; i < array.length(); i++) { 
							JSONArray object = array.getJSONArray(i);
							TextView transaction = new TextView(self);
							String requester = object.getString(0);
							String request = object.getString(1);
							String amount = object.getString(2);
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
