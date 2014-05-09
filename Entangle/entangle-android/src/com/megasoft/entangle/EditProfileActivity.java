package com.megasoft.entangle;

import java.io.InputStream;

import com.megasoft.config.Config;

import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.annotation.TargetApi;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.Build;

public class EditProfileActivity extends Activity {
	private static final int REQUEST_ID = 1;
	private Button browse;
	private Button upload;
	private Bitmap picture;
	private String sessionId;
	private SharedPreferences settings;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_edit_profile);
		// Show the Up button in the action bar.
		setupActionBar();
		browse = (Button) findViewById(R.id.browse_button);
        upload = (Button) findViewById(R.id.upload_button);
        browse.setOnClickListener(browseHandler);
        upload.setOnClickListener(uploadHandler);
        this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
	}
	/**
	 * action taken when find a photo button is clicked
	 * @author Nader Nessem
	 */
	View.OnClickListener browseHandler = new View.OnClickListener() {
	    public void onClick(View v) {
	    	Intent intent = new Intent();
		    intent.setAction(Intent.ACTION_GET_CONTENT);
		    intent.addCategory(Intent.CATEGORY_OPENABLE);
		    intent.setType("image/*");
			startActivityForResult(intent, REQUEST_ID);
	    }
	 };
	 View.OnClickListener uploadHandler = new View.OnClickListener() {
	    public void onClick(View v) {
	      
	    }
	 };
	 protected void onActivityResult(int requestCode, int resultCode, Intent data) {
			InputStream stream = null;
		    if (requestCode == REQUEST_ID && resultCode == Activity.RESULT_OK) {
		    	try {
		    		stream = getContentResolver().openInputStream(data.getData());
		    		picture = BitmapFactory.decodeStream(stream);
		    		((ImageView)findViewById(R.id.image_holder)).setImageBitmap(Bitmap.createScaledBitmap(picture, 
		    				picture.getWidth()/2, picture.getHeight()/2, true));
		    	} catch (Exception e) {
		    		e.printStackTrace();
		    	}
		    	if (stream != null) {
		    		try {
		    			stream.close();
		    		} catch (Exception e) {
		    			e.printStackTrace();
		    		}
		    	}
			}
		}

	/**
	 * Set up the {@link android.app.ActionBar}, if the API is available.
	 */
	@TargetApi(Build.VERSION_CODES.HONEYCOMB)
	private void setupActionBar() {
		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB) {
			getActionBar().setDisplayHomeAsUpEnabled(true);
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.edit_profile, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId()) {
		case android.R.id.home:
			// This ID represents the Home or Up button. In the case of this
			// activity, the Up button is shown. Use NavUtils to allow users
			// to navigate up one level in the application structure. For
			// more details, see the Navigation pattern on Android Design:
			//
			// http://developer.android.com/design/patterns/navigation.html#up-vs-back
			//
			return true;
		}
		return super.onOptionsItemSelected(item);
	}

}
