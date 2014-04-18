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
	
	

package com.megasoft.entangle;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.content.Intent;
import android.view.Menu;

public class MainActivity extends Activity {
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {		
		
		setContentView(R.layout.activity_main);
		Intent intent = new Intent(this, Request.class);
		startActivity(intent);
		startActivity((new Intent(this,InviteUserActivity.class)).putExtra("com.megasoft.entangle.tangleId", 2));
	}


	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true; 
	}
	

}
