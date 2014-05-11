package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.content.Context;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.Toast;

public class CreateTangleActivity extends Activity {

	/**
	 * App's shared preferences.
	 */
	public SharedPreferences settings;

	/**
	 * User's session id.
	 */
	private String sessionId;

	/**
	 * An array of the ids of the request icons available in the app.
	 */
	private static Integer[] imageIconDatabase = { R.drawable.aatrox,
			R.drawable.ahri, R.drawable.akali, R.drawable.amumu,
			R.drawable.zac, R.drawable.ziggs };

	/**
	 * An array of the names of the request icons available in the app.
	 */
	private String[] imageNameDatabase = { "aatrox", "ahri", "akali", "amumu",
			"zac", "ziggs" };

	/**
	 * A number indicating the currently selected icon by the user in the
	 * spinner.
	 */
	private int usedIcon;

	@Override
	/**
	 * When the activity is created, it sets up listeners to the spinner.
	 * @param Bundle savedInstanceState
	 * @author Mansour
	 */
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_create_tangle);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		Spinner iconSpinner = (Spinner) findViewById(R.id.iconSpinner);
		iconSpinner.setAdapter(new TangleIconSpinnerAdapter(
				CreateTangleActivity.this, R.layout.spinner_icons,
				imageNameDatabase));
		iconSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {

			@Override
			public void onItemSelected(AdapterView<?> arg0, View arg1,
					int arg2, long arg3) {
				usedIcon = arg0.getSelectedItemPosition();
			}

			@Override
			public void onNothingSelected(AdapterView<?> arg0) {
			}
		});
		usedIcon = iconSpinner.getSelectedItemPosition();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.create_tangle, menu);
		return true;
	}

	/**
	 * Creates the tangle when Create button is clicked.
	 * 
	 * @param View
	 *            view
	 * @author Mansour
	 */
	public void create(View view) {
		EditText tangleName = (EditText) findViewById(R.id.tangleName);
		EditText tangleDescription = (EditText) findViewById(R.id.tangleDescription);
		if (tangleName.getText().toString().equals("")) {
			Toast.makeText(getApplicationContext(), "Tangle Name Is Empty",
					Toast.LENGTH_SHORT).show();
		} else {
			if (tangleDescription.getText().toString().equals("")) {
				Toast.makeText(getApplicationContext(), "Descritpion Is Empty",
						Toast.LENGTH_SHORT).show();
			} else {
				sendTangleToServer();
			}
		}
	}

	/**
	 * Sends the tangle info to the server.
	 * @author Mansour
	 */
	public void sendTangleToServer() {
		PostRequest tanglePostRequest = new PostRequest(Config.API_BASE_URL
				+ "/tangle") {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					EditText tangleName = (EditText) findViewById(R.id.tangleName);
					tangleName.setError("Tangle Is Already Taken");
				} else {
					if (!(this.getStatusCode() == 201)) {
						Toast.makeText(getApplicationContext(),
								"Try Again Later", Toast.LENGTH_LONG).show();
					} else {
						goToHomePage();
					}
				}
			}
		};
		JSONObject tangleJSON = new JSONObject();
		try {
			tangleJSON.put("tangleName",
					((EditText) findViewById(R.id.tangleName)).getText()
							.toString());
			tangleJSON.put("tangleIcon", usedIcon);
			tangleJSON.put("tangleDescription",
					((EditText) findViewById(R.id.tangleDescription)).getText()
							.toString());
		} catch (JSONException e) {
			e.printStackTrace();
		}
		tanglePostRequest.setBody(tangleJSON);
		tanglePostRequest.addHeader(Config.API_SESSION_ID, sessionId);
		tanglePostRequest.execute();
	}

	/**
	 * Shows a toast informing that the tangle is created and closing the activity.
	 * @author Mansour
	 */
	public void goToHomePage() {
		Toast.makeText(getApplicationContext(), "Your Tangle Has Been Created",
				Toast.LENGTH_LONG).show();
		this.finish();
	}

	/**
	 * A private class that defines the adapter for the icons' spinner.
	 * @author Mansour
	 */
	private class TangleIconSpinnerAdapter extends ArrayAdapter<String> {

		public TangleIconSpinnerAdapter(Context context,
				int textViewResourceId, String[] objects) {
			super(context, textViewResourceId, objects);
		}

		@Override
		public View getDropDownView(int position, View convertView,
				ViewGroup parent) {
			return getCustomView(position, convertView, parent);
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			return getCustomView(position, convertView, parent);
		}

		public View getCustomView(int position, View convertView,
				ViewGroup parent) {

			LayoutInflater inflater = getLayoutInflater();
			View row = inflater.inflate(R.layout.spinner_icons, parent, false);

			ImageView icon = (ImageView) row.findViewById(R.id.icon);
			icon.setImageResource(imageIconDatabase[position]);

			return row;
		}
	}
}
