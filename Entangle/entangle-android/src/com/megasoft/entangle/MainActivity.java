package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;
import com.megasoft.requests.GetRequest;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.content.Intent;
import android.view.Menu;
import android.view.View;
import android.widget.Toast;

public class MainActivity extends Activity {
	String offererMail;
	String tangleOwnerMail;
	@Override
	protected void onCreate(Bundle savedInstanceState) {		
		super.onCreate(savedInstanceState);
<<<<<<< HEAD
		setContentView(R.layout.request);	
	}	
	
	public void startClaimForm(View v) {	
		final Intent intent = new Intent(this, claim.class);
		String sessionID = (String) intent.getCharSequenceExtra("sessionID");	
		GetRequest requestOffererMail = new GetRequest("http://entangle2.apiary.io/tangle/" + sessionID + "/getRecieverMail"){
			
			protected void onPostExecute(String response) {
				try {
					JSONObject object = new JSONObject(response);
					offererMail= object.getString("X-OFFERER-MAIL");
					intent.putExtra("sender", offererMail);
				}
				catch (JSONException e) {
					e.printStackTrace();
				}
			}
		};
		
		String tangleID = (String) intent.getCharSequenceExtra("tangleID");
		GetRequest requestTangleOwnerMail = new GetRequest("http://entangle2.apiary.io/tangle/" + tangleID + "/getRecieverMail"){
			
			protected void onPostExecute(String response) {
				try {
					JSONObject object = new JSONObject(response);
					tangleOwnerMail = object.getString("X-TANGLEOWNER-MAIL");
					intent.putExtra("sender", tangleOwnerMail);
				}
				catch (JSONException e) {
					e.printStackTrace();
				}
			}
		};
		Toast.makeText(this, "Loading Claim Form", Toast.LENGTH_SHORT).show();
		startActivity(intent);
	
	}
	
=======
		setContentView(R.layout.activity_main);

		startActivity((new Intent(this,InviteUserActivity.class)).putExtra("com.megasoft.entangle.tangleId", 2));
	}

>>>>>>> 17e4189a346e1cb051113733c77d331599138711
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true; 
	}
	

}
