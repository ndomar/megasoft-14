package com.megasoft.entangle;

import java.io.ByteArrayOutputStream;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.entangle.R;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;

import android.net.Uri;
import android.os.Bundle;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Base64;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

public class CreateTangleActivity extends Activity {

	private static final int REQUEST_CODE = 1;
	public static final int BUTTON_POSITIVE = 0xffffffff;
	public static final int GREEN = 0xff00ff00;
	public static final int RED = 0xffff0000;
	public static final int BLACK = 0xff000000;
	String encodedImage;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_create_tangle);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.create_tangle, menu);
		EditText tangleName = (EditText) findViewById(R.id.tangleName);
		tangleName.addTextChangedListener(new TextWatcher() {
			
			@Override
			public void afterTextChanged(Editable arg0) {
				resetColor(R.id.tangleName);
			}

			@Override
			public void beforeTextChanged(CharSequence s, int start, int count,
					int after) {
			}

			@Override
			public void onTextChanged(CharSequence s, int start, int before,
					int count) {
			}
		});
		return true;
	}

	// Checks whether the tangle name already exists or not
	public void checkTangleName(View view) {
		String tangleNameText = ((EditText) findViewById(R.id.tangleName))
				.getText().toString();
		if((tangleNameText.split(" ")).length > 1){
			showMessage("TANGLE NAME SHOULD BE ONE WORD");
		}else{
			GetRequest getNameRequest = new GetRequest(
					"http://entangle2.apiary-mock.com/tangle/check/"
							+ tangleNameText) {
				protected void onPostExecute(String response) {
					if (!(this.getStatusCode() == 404)) {
						insertAvailability(false, R.id.tangleName);
					} else {
						insertAvailability(true, R.id.tangleName);
					}
				}
			};
			getNameRequest.addHeader("X-SESSION-ID", "fdgdf");
			getNameRequest.execute();
		}
	}

	// when the user needs to choose an icon for his tangle it redirects him to
	// the gallery
	public void chooseIcon(View view) {
		goToGallery();
	}

	// opens the gallery for the user to pick a picture
	public void goToGallery() {
		startActivityForResult(new Intent(Intent.ACTION_PICK,
				android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI),
				REQUEST_CODE);
	}

	// waits for the resulted pick of the user from the gallery, set the icon in
	// the activity to it and encode the image in base64
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		if (resultCode == RESULT_OK && requestCode == REQUEST_CODE
				&& null != data) {
			Bitmap bitmap = getPhotoPath(data.getData());
			ImageView imageView = (ImageView) findViewById(R.id.icon);
			imageView.setImageBitmap(bitmap);
			ByteArrayOutputStream baos = new ByteArrayOutputStream();
			bitmap.compress(Bitmap.CompressFormat.JPEG, 100, baos);
			byte[] byteArray = baos.toByteArray();
			encodedImage = Base64.encodeToString(byteArray, Base64.DEFAULT);
		}
	}

	// returns a bitmap of the image that the user chose from the gallery
	public Bitmap getPhotoPath(Uri uri) {
		String[] projection = { android.provider.MediaStore.Images.Media.DATA };
		Cursor cursor = getContentResolver().query(uri, projection, null, null,
				null);
		int columnIndex = cursor.getColumnIndexOrThrow(projection[0]);
		cursor.moveToFirst();
		String filePath = cursor.getString(columnIndex);
		cursor.close();
		Bitmap bitmap = BitmapFactory.decodeFile(filePath);
		return bitmap;
	}

	// checks if every thing is correcting before creating
	public void Create(View view) {
		EditText tangleName = (EditText) findViewById(R.id.tangleName);
		ImageView tangleIcon = (ImageView) findViewById(R.id.icon);
		if ((tangleName.getText().toString()).equals("")) {
			showMessage("PLEASE ENTER A TANLGE NAME");
		} else {
			if (tangleName.getCurrentTextColor() == BLACK) {
				showMessage("PLEASE CHECK IF THE TANGLE NAME IS AVAILABLE FIRST");
			} else {
				if (tangleName.getCurrentTextColor() == RED) {
					showMessage("TANGLE NAME UNAVAILABLE, PLEASE CHOOSE ANOTHER NAME");
				} else {
					if (tangleIcon.getDrawable() == null) {
						showMessage("PLEASE CHOOSE A TANGLE ICON FIRST");
					} else {
						sendImageToServer();
					}
				}
			}
		}
	}

	// sends the new tangle info to the server
	public void sendImageToServer() {
		PostRequest imagePostRequest = new PostRequest(
				"http://entangle2.apiary-mock.com/tangle") {
			protected void onPostExecute(String response) {
				if (!(this.getStatusCode() == 201)) {
					showMessage("ERROR, TRY AGAIN LATER");
				} else {
					goToHomePage();
				}
			}
		};
		JSONObject imageJSON = new JSONObject();
		try {
			imageJSON.put("tangleName",
					((EditText) findViewById(R.id.tangleName)).getText()
							.toString());
			imageJSON.put("Image", encodedImage);
		} catch (JSONException e) {
			e.printStackTrace();
		}
		imagePostRequest.setBody(imageJSON);
		imagePostRequest.addHeader("X-SESSION-ID", "fgdzr");
		imagePostRequest.execute();
	}

	// shows a message dialog to the user
	public void showMessage(String message) {
		AlertDialog ad = new AlertDialog.Builder(this).create();
		ad.setCancelable(false);
		ad.setMessage(message);
		ad.setButton(BUTTON_POSITIVE, "OK",
				new DialogInterface.OnClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which) {
						dialog.dismiss();
					}
				});
		ad.show();
	}

	// resets the color of a text to black
	public void resetColor(int id) {
		TextView textView = (TextView) findViewById(id);
		textView.setTextColor(BLACK);
	}

	// change the color of a text to either green or red
	public void insertAvailability(boolean available, int id) {

		TextView textView = (TextView) findViewById(id);
		if (available == true) {
			textView.setTextColor(GREEN);
		} else {
			textView.setTextColor(RED);
		}
	}

	// redirects the user to the home page after creating a tangle
	public void goToHomePage() {
		AlertDialog ad = new AlertDialog.Builder(this).create();
		ad.setCancelable(false);
		ad.setMessage("CONGRATULATIONS, YOUR TANGLE IS CREATED");
		ad.setButton(BUTTON_POSITIVE, "OK",
				new DialogInterface.OnClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which) {
						dialog.dismiss();
						goToHomeHelper();
					}
				});
		ad.show();
	}
	
	public void goToHomeHelper(){
		startActivity(new Intent(this, HomePage.class));
	}
}
