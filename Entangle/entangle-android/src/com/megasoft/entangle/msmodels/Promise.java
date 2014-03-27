package com.megasoft.entangle.msmodels;

import com.megasoft.entangle.msmodels.interfaces.AlwaysCallback;
import com.megasoft.entangle.msmodels.interfaces.DoneCallback;
import com.megasoft.entangle.msmodels.interfaces.FailCallback;

public class Promise {

	private DoneCallback done;
	private FailCallback fail;
	private AlwaysCallback always;
	
	public Promise done(DoneCallback done) {
		this.done = done;
		return this;
	}
	
	public Promise fail(FailCallback fail) {
		this.fail = fail;
		return this;
	}
	
	public Promise always(AlwaysCallback always) {
		this.always = always;
		return this;
	}
	
	protected void resolve(String response) {
		if (done != null) {
			done.onDone(response);
		}
		if (always != null) {
			always.onAlways(response);
		}
	}
	
	protected void reject(Object response) {
		if (fail != null) {
			fail.onFail(response);
		}
		if (always != null) {
			always.onAlways(response.toString());
		}
	}
	
}
