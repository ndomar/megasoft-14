package com.megasoft.entangle;

import java.io.ByteArrayOutputStream;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.util.Base64;
import android.app.Activity;
import android.content.Context;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
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

	public static final int BUTTON_POSITIVE = 0xffffffff;
	public static final int GREEN = 0xff00ff00;
	public static final int RED = 0xffff0000;
	public static final int BLACK = 0xff000000;
	String encodedImage;
	String sessionId;
	SharedPreferences settings;

	private static Integer[] imageIconDatabase = { R.drawable.aatrox,
			R.drawable.ahri, R.drawable.akali, R.drawable.amumu,
			R.drawable.zac, R.drawable.ziggs };

	private String[] imageNameDatabase = { "aatrox", "ahri", "akali", "amumu",
			"zac", "ziggs" };

	private int usedIcon;

	@Override
	/**
	 * When the activity is created, it sets up listeners to the tangle name field.
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

	public void encodeToBase64(Bitmap bitmap) {
		ByteArrayOutputStream baos = new ByteArrayOutputStream();
		bitmap.compress(Bitmap.CompressFormat.PNG, 100, baos);
		byte[] byteArray = baos.toByteArray();
		encodedImage = Base64.encodeToString(byteArray, Base64.DEFAULT);
	}

	/**
	 * Creates the tangle when Create button is clicked.
	 * 
	 * @param view
	 * @author Mansour
	 */
	public void create(View view) {
		EditText tangleName = (EditText) findViewById(R.id.tangleName);
		EditText tangleDescription = (EditText) findViewById(R.id.tangleDescription);
		if (tangleName.getText().toString().equals("")) {
			Toast.makeText(getApplicationContext(), "Tangle Name Is Empty",
					Toast.LENGTH_LONG).show();
		} else {
			if (tangleName.getCurrentTextColor() == RED) {
				Toast.makeText(getApplicationContext(),
						"Tangle Name Unavailable", Toast.LENGTH_LONG).show();
			} else {
				if (tangleDescription.getText().toString().equals("")) {
					Toast.makeText(getApplicationContext(),
							"Descritpion Is Empty", Toast.LENGTH_LONG).show();
				} else {
					Bitmap bitmap = BitmapFactory.decodeResource(
							getResources(), imageIconDatabase[usedIcon]);
					encodeToBase64(bitmap);
					sendTangleToServer();
				}
			}
		}
	}

	/**
	 * Sends the tangle info to the server.
	 * 
	 * @author Mansour
	 */
	public void sendTangleToServer() {
		PostRequest imagePostRequest = new PostRequest(Config.API_BASE_URL
				+ "/tangle") {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					EditText tangleName = (EditText) findViewById(R.id.tangleName);
					tangleName.setError("Tangle Is Already Taken");
				} else {
					if (!(this.getStatusCode() == 201)) {
						Toast.makeText(getApplicationContext(),
								"Try Again Later" + this.getStatusCode(),
								Toast.LENGTH_LONG).show();
					} else {
						goToHomePage();
					}
				}
			}
		};
		JSONObject imageJSON = new JSONObject();
		try {
			imageJSON.put("tangleName",
					((EditText) findViewById(R.id.tangleName)).getText()
							.toString());
			imageJSON.put("tangleIcon", encodedImage);
		} catch (JSONException e) {
			e.printStackTrace();
		}
		imagePostRequest.setBody(imageJSON);
		imagePostRequest.addHeader(Config.API_SESSION_ID, sessionId);
		imagePostRequest.execute();
	}

	/**
	 * Shows a dialogue informing that the tangle is created and redirecting to
	 * homepage when pressing OK.
	 * 
	 * @author Mansour
	 */
	public void goToHomePage() {
		Toast.makeText(getApplicationContext(), "Your Tangle Has Been Created",
				Toast.LENGTH_LONG).show();
		this.finish();
	}

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
